# PHP Math Parser

A math expression parser for PHP. Some tidy up needed remove unnecessary classes
for example.

**NOTE** The code currently parses the wrong way round, instead I should be checking the whole 
expression for a pattern match, rather than check chunks... Folding doesn't work.

# Syntax Analysis

	1 + 1 / 2

Given the rules:

	S -> float
	S -> integer
	S -> S add S
	S -> S minus S
	S -> S multiply S
	S -> S divide S
	S -> S exponent S
	S -> openparen S closeparen

Instead of trying to match chunks of the original string, it should attempt to pattern match
the entire string, it will have to resolve for non-terminals...

Check the expression `1 + 1 / 2` starting with the start symbol `S`. We have already resolved
the string to tokens `integer add integer divide integer`. The algorithm first checks the
patterns that match `S` in order from top to bottom: `S -> float`, `S -> integer`, which 
both result in no match. These were easy to resolve a simple check. 

The next pattern is `S -> S add S`, now this becomes more complicated as the pattern is a
mix of terminals and non-terminals. The algorithm should understand that the `S` symbol has 
to be a chain of tokens where the minimum length of the chain is 1 and there is no maximum 
length, but the `add` symbol is the least expensive symbol to check. 

It makes most sense to check the tokens from index 2 to the second to last index against a 
match for the `add` pattern. In this case the first token it checks is a match. It will now 
verify `S add S`, where the first S is `1` and the last S is `1 / 2`. 

Running through the rules again from the top on the first `S` we get a match at `S -> integer`. 
For the last `S`, we again run through the rules from the top, we get to the first rule with 
non-terminals. The check becomes much more simple as there are only three tokens left in this 
expression and the pattern is a minimum of three tokens the algorithm only needs to check against 
one token `divide`, `S add S` is not a match, we get to `S divide S` and it is a match. We now have
resolved `integer add S divide S`. The left hand `S` of the expression is `1` which is resolved to 
`S -> integer`, the right hand `S` of the expression is `2` is also resolved to `S -> integer`.

The order in which the algorithm will resolve the expression

	S(integer add integer divide integer)

	S

	S(integer) add S(integer divide integer)

	[ add ]
       |
     -----
     |   |
     S   S

	integer add S(integer divide integer)

	     [ add ]
	        |
         --------
         |      |
    [ integer ] S

	integer add S(integer) divide S(integer)

	        [ add ]
	           |
         ------------
         |          |
    [ integer ] [ divide ]
                    |
                  -----
                  |   |
                  S   S

	integer add integer divide S(integer)

	        [ add ]
	           |
         ------------
         |          |
    [ integer ] [ divide ]
                    |
                 --------
                 |      |
            [ integer ] S	         

    integer add integer divide integer

	        [ add ]
	           |
         ------------
         |          |
    [ integer ] [ divide ]
                    |
              -------------
              |           |
         [ integer ] [ integer ]

The complexities are in resolving cyclic non-terminal symbols, in dividing the tokens into the 
possible combinations for checking. As mentioned above this is the process of resolving non-terminal
symbols. To subdivide the tokens for matching requires a bit of intelligence, we will need to do
some preanalysis on the grammar to understand the optimal approach to matching the patterns. Given
the following rules:

	S -> float
	S -> integer
	S -> S add S
	S -> S minus S
	S -> S multiply S
	S -> S divide S
	S -> S exponent S
	S -> openparen S closeparen

We can perform preanalysis of the grammar to work out the minimum and maximum sequence lengths
which I will express as a tuple (min, max). The terminals are simple to work out, (1,1):

	S -> (1,1)
	S -> (1,1)
	S -> S (1,1) S
	S -> S (1,1) S
	S -> S (1,1) S
	S -> S (1,1) S
	S -> S (1,1) S
	S -> (1,1) S (1,1)

With the terminals worked out we can now work out the non-terminals, or in this case just `S` which
has a minimum length pattern as a length of `1`, there is no maximum length as the rule is cyclic.

    S -> (1,1)
    S -> (1,1)
    S -> (1,*) (1,1) (1,*)
    S -> (1,*) (1,1) (1,*)
    S -> (1,*) (1,1) (1,*)
    S -> (1,*) (1,1) (1,*)
    S -> (1,*) (1,1) (1,*)
    S -> (1,1) (1,*) (1,1)

With this information we can split up the tokens easily, for example:

	tokens = {integer minus integer plus integer}

	S -> S plus S

From this pattern we know the minimum number of tokens needed to match this pattern is `3`, we also
know that the `plus` symbol must be exactly `1` token long. It could be split up as

	{integer} {minus} {integer plus integer}

OR...

	{integer minus} {integer} {plus integer}

OR...

	{integer minus integer} {plus} {integer}

Always check the terminals first as this is the least expensive check. In the above example the
algorithm should be able to determine that it only needs to check for the terminal symbol in the
middle three tokens:

    {minus, integer, plus}

The result of a positive match will determine the split for subsequent pattern matching. So it matches
a `plus` and the next two arrays of tokens `{integer minus integer}` and `{integer}` are passed to the
next round of pattern matching (A failure to match submatches will return to this step to continue 
subsequent pattern matchin).
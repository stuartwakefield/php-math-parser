# PHP Math Parser

A math expression parser for PHP. Some tidy up needed remove unnecessary classes
for example.

## Info about syntax analysis

It works with operator precedence, for example:

    1 + 1 / 2
    | | | | |
    | | +-+-+
    | |   |  
    | |  0.5
    | |   |
    +-+---+
      |
     1.5

    1 / 2 + 1
    | | | | |
    +-+-+ | |
      |   | |
     0.5  | |
      |   | |
      +---+-+
          |
         1.5

    ( 1 + 1 ) / 2
      | | |   | |
      +-+-+   | |
        |     | |
        2     | |
        |     | |
        +-----+-+
              |
              1

    ( 2 * ( 1 + 2 ) ) / 3
      | |   | | |     | |
      | |   +-+-+     | |
      | |     |       | |
      | |     3       | |
      | |     |       | |
      +-+-----+       | |
        |             | |
        6             | |
        |             | |
        +-------------+-+
                      |
                      2

The patterns are:

Our token definitions:

    openparen
    closeparen
    number
    exponent
    root
    multiply
    divide
    addition
    subtract
    
Our syntax patterns:

    p = openparen o closeparen
    e = o (exponent|root) o
    m = o (multiply|divide) o
    a = o (add|subtract) o
    o = (number|p|e|m|a)
    

Simple example `1 + 1`:

    # original tokens
    [ number
    , add
    , number ]

    # pass 1
    [ o(number)
    , add
    , o(number) ]

    # pass 2
    [ o(a(o(number), add, o(number))) ]

A more complex example `1 + 1 / 2`:

    # original tokens
    [ number
    , add
    , number
    , divide
    , number ]

    # pass 1
    [ o(number)
    , add
    , o(number)
    , divide
    , o(number) ]

    # pass 2
    [ o(number)
    , add
    , o(m(o(number), divide, o(number))) ]

    # pass 3
    [ o(a(o(number), o(m(o(number), divide, o(number))))) ]

An even more complex example `(2 * (2 + 1)) / 3`:

    # original tokens
    [ openparen 
    , number 
    , multiply 
    , openparen
    , number
    , add
    , number
    , closeparen
    , closeparen
    , divide
    , number ]

    # first pass
    [ openparen 
    , o(number)
    , multiply 
    , openparen
    , o(number)
    , add
    , o(number)
    , closeparen
    , closeparen
    , divide
    , o(number) ]

    # second pass
    [ openparen 
    , o(number)
    , multiply 
    , openparen
    , o(a(o(number), add, o(number)))
    , closeparen
    , closeparen
    , divide
    , o(number) ]

    # third pass
    [ openparen 
    , o(number)
    , multiply 
    , o(p(openparen, o(a(o(number), add, o(number))), closeparen))
    , closeparen
    , divide
    , o(number) ]

    # fourth pass
    [ openparen 
    , o(m(o(number), multiply, o(p(openparen, o(a(o(number), add, o(number))), closeparen))))
    , closeparen
    , divide
    , o(number) ]

    # fifth pass
    [ o(p(openparen, o(m(o(number), multiply, o(p(openparen, o(a(o(number), add, o(number))), closeparen)))), closeparen))
    , divide
    , o(number) ]

    # sixth pass
    [ o(d(o(p(openparen, o(m(o(number), multiply, o(p(openparen, o(a(o(number), add, o(number))), closeparen)))), closeparen)), divide, o(number))) ]

**NOTE** I'm doing it the wrong way round, instead I should be checking the whole expression
for a pattern match, rather than check chunks... Folding doesn't work.

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
length, but the `add` symbol is the least expensive symbol to check. It makes most sense to 
check the tokens from index 2 to the second to last index against a match for the `add` 
pattern. In this case the first token it checks is a match. It will now verify `S add S`, 
where the first S is `1` and the last S is `1 / 2`. Running through the rules again from the 
top on the first `S` we get a match at `S -> integer`. For the last `S`, we again run 
through the rules from the top, we get to the first rule with non-terminals. The check 
becomes much more simple as there are only three tokens left in this expression and the 
pattern is a minimum of three tokens the algorithm only needs to check against one token 
`divide`, `S add S` is not a match, we get to `S divide S` and it is a match. We now have
resolved `integer add S divide S`. The left hand `S` of the expression is `1` which is
resolved to `S -> integer`, the right hand `S` of the expression is `2` is also resolved to
`S -> integer`.

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
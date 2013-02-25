# PHP Math Parser

**NOTE** I'm doing it the wrong way round, instead I should be checking the whole expression
for a pattern match, rather than check chunks... Folding doesn't work.

    1 + 1 / 2

1. `N = /\d*\.\d+/`
2. `N = /\d+/`
3. `O = N`
4. `P = '*'`
5. `P = '/'`
6. `S = '+'`
7. `S = '-'`
8. `O = O S O`
9. `O = O P O`
10. `O = '(' O ')'`

The first match is rule 8. The expression does not match for any patterns prior to rule 8, at
rule 8, the S matches the `+` sign and the O on the left matches the `1`, the O on the right
matches `1 / 2`. So now we have `O + O` and two sides left to resolve. The next match will
probably be rule 3. Followed by rule 9, followed by rule three twice.

    O
    O + O
    1 + O
    1 + O / O
    1 + 1 / O
    1 + 1 / 2    

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

# PHP Math Parser

A math expression parser for PHP. Needs some work on the syntax analysis in
`lib/math.php`.

Currently the syntax analysis does nothing more than this:

    1 + 1
	| | |
    +-+-+
      |
      2

It will need to work with operator precedence, for example:

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
    exponentoperator
    rootoperator
    multiplyoperator
    divideoperator
    additionoperator
    subtractoperator
	
Our syntax patterns:

    expop = operand (exponent|root) operand
    mulop = operand (multiply|divide) operand
    addop = operand (add|subtract) operand
    paren = openparen operation closeparen
    operand = (paren|number)
    operation = (expop|mulop|addop)

Our tokens:

    [ openparen 
    , number 
    , multiplyoperator 
    , openparen
    , number
    , additionoperator
    , number
    , closeparen
    , closeparen
    , divideoperator
    , number ]

Check for pattern E, no matches, pattern M, two matches:

    [ openparen
    , number
    , M(multiplyoperator)
    , openparen
    , number
    , additionoperator
    , number
    , closeparen
    , closeparen
    , M(divideoperator)
    , number ]

Check for pattern A, one match:

    [ openparen
    , number
    , M(multiplyoperator)
    , openparen
    , number
    , A(additionoperator)
    , number
    , closeparen
    , closeparen
    , M(divideoperator)
    , number ]

Check for pattern operator, three matches:

    [ openparen
    , number
    , operator(M(multiplyoperator))
    , openparen
    , number
    , operator(A(additionoperator))
    , number
    , closeparen
    , closeparen
    , operator(M(divideoperator))
    , number ]

Check for pattern paren, no matches, check for pattern operand, four matches:

    [ openparen
    , operand(number)
    , operator(M(multiplyoperator))
    , openparen
    , operand(number)
    , operator(A(additionoperator))
    , operand(number)
    , closeparen
    , closeparen
    , operator(M(divideoperator))
    , operand(number) ]

Check for pattern operation, two matches:

    [ openparen
    , operand(number)
    , operator(M(multiplyoperator))
    , openparen
    , operand(number)
    , operation(operator(A(additionoperator)), operand(number))
    , closeparen
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

Check for pattern statement, one match:

    [ openparen
    , operand(number)
    , operator(M(multiplyoperator))
    , openparen
    , statement(operand(number), operation(operator(A(additionoperator)), operand(number)))
    , closeparen
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

Matches found in this matching session and array length > 1, run next pattern matching session, no Es, no Ms, no As, no operators, one paren:

    [ openparen
    , operand(number)
    , operator(M(multiplyoperator))
    , paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen)
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

One match for operand:

    [ openparen
    , operand(number)
    , operator(M(multiplyoperator))
    , operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen))
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

One match for operation:

    [ openparen
    , operand(number)
    , operation(operator(M(multiplyoperator)), operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen)))
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

One match for statement:

    [ openparen
    , statement(operand(number), operation(operator(M(multiplyoperator)), operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen))))
    , closeparen
    , operation(operator(M(divideoperator)), operand(number)) ]

Matches found in this session and array length > 1, run next pattern matching session... No Es, Ms, As, operators, one paren:

    [ paren(openparen, statement(operand(number), operation(operator(M(multiplyoperator)), operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen)))), closeparen)
    , operation(operator(M(divideoperator)), operand(number)) ]

Match for operand:

    [ operand(paren(openparen, statement(operand(number), operation(operator(M(multiplyoperator)), operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen)))), closeparen))
    , operation(operator(M(divideoperator)), operand(number)) ]

No matches for operation, one match for statement:

	[ statement(operand(paren(openparen, statement(operand(number), operation(operator(M(multiplyoperator)), operand(paren(openparen, statement(operand(number), operation(operator(A(additionoperator)), operand(number))), closeparen)))), closeparen)), operation(operator(M(divideoperator)), operand(number))) ]

Array length == 1 and item is a statement, syntax analysis complete! Not quite, it doesn't tackle operator
precedence.

    [ statement(operand(number), operation(operator(A(additionoperator)), operand(number)))
    , operation(operator(M(divisionoperator)), operand(number)) ]

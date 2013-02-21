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
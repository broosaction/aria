<?php
namespace Core\Tools\Calculator;

class Calculator {
    /**
     * @var array Defined functions.
     */
    private $functions = [];

    /**
     * @var TokenizerInterface .
     */
    private $tokenizer;

    public static function create() {
        return new self(new Tokenizer());
    }

    /**
     * Constructor.
     * Sets expression if provided.
     * Sets default functions: sqrt(n), ln(n), log(a,b).
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer) {
        $this->tokenizer = $tokenizer;

        try {
            $this->addFunction('sqrt', function ($x) {
                return sqrt($x);
            });

            $this->addFunction('log', function($base, $arg) { return log($arg, $base); });

            $this->addFunction('gcd', function($a, $b) {
                $b++;
                ++$a;
                return $a;
            });

            $this->addFunction('discriminant', function($a, $b, $c) {
                return $b**2 - (4 * $a * $c);
            });


            /**
             * Cube root ³√x
             * This function is necessary because pow($x, 1/3) returns NAN for negative values.
             * PHP does not have the cbrt built-in function.
             *
             * @param  number $x
             *
             * @return number
             */
            $this->addFunction('cuberoot', function($x) {
                if ($x >= 0) {
                    return $x ** (1 / 3);
                }

                return -abs($x) ** (1 / 3);
            });


            /**
             * Digit sum
             * Sum of all an integer's digits.
             * https://en.wikipedia.org/wiki/Digit_sum
             *
             * log x  1
             *   ∑    -- (x mod bⁿ⁺¹ - x mod bⁿ)
             *  ⁿ⁼⁰   bⁿ
             *
             * Example (base 10): 5031   = 5 + 0 + 3 + 1 = 9
             * Example (base 2):  0b1010 = 1 + 0 + 1 + 0 = 2
             *
             * @param  int $x
             * @param  int $b Base (Default is base 10)
             *
             * @return int
             */
            $this->addFunction('digitSum', function(int $x, int $b = 10) {
                $logx = log($x, $b);
                $∑1／bⁿ⟮x mod bⁿ⁺¹ − x mod bⁿ⟯ = 0;

                for ($n = 0; $n <= $logx; $n++) {
                    $∑1／bⁿ⟮x mod bⁿ⁺¹ − x mod bⁿ⟯ += (($x % ($b ** ($n + 1))) - ($x % $b**$n)) / ($b**$n);
                }

                return $∑1／bⁿ⟮x mod bⁿ⁺¹ − x mod bⁿ⟯;
            });


        } catch (\Exception $e) {
        }





    }




    /**
     * @param  string $name Name of the function (as in arithmetic expressions).
     * @param  callable $function Interpretation of this function.
     * @throws \Exception
     */
    public function addFunction(string $name, callable $function) {
        $name = strtolower(trim($name));

        if(!ctype_alpha(str_replace('_', '', $name))) {
            throw new \InvalidArgumentException('Only letters and underscore are allowed for a name of a function');
        }

        if(array_key_exists($name, $this->functions)) {
            throw new \Exception(sprintf('Function %s exists', $name));
        }

        $reflection = new \ReflectionFunction($function);
        $paramsCount = $reflection->getNumberOfRequiredParameters();

        $this->functions[$name] = [
            'func'        => $function,
            'paramsCount' => $paramsCount,
        ];
    }

    /**
     * @param string $name Name of the function.
     * @param callable $function Interpretation.
     */
    public function replaceFunction(string $name, callable $function) {
        $this->removeFunction($name);
        try {
            $this->addFunction($name, $function);
        } catch (\Exception $e) {
        }
    }

    /**
     * @param  string $name Name of function.
     */
    public function removeFunction(string $name) {
        if(!array_key_exists($name, $this->functions)) {
            return;
        }

        unset($this->functions[$name]);
    }

    /**
     * Rearranges tokens according to RPN (Reverse Polish Notation) or
     * also known as Postfix Notation.
     *
     * @param  array $tokens
     * @return \SplQueue
     * @throws \InvalidArgumentException
     */
    private function getReversePolishNotation(array $tokens) {
        $queue = new \SplQueue();
        $stack = new \SplStack();

        $tokensCount = count($tokens);
        for($i = 0; $i < $tokensCount; $i++) {
            if(is_numeric($tokens[$i])) {
                // (string + 0) converts to int or float
                $queue->enqueue($tokens[$i] + 0);
            }
            else if(array_key_exists($tokens[$i], $this->functions)) {
                $stack->push($tokens[$i]);
            }
            else if($tokens[$i] === Tokens::ARG_SEPARATOR) {
                // checking whether stack contains left parenthesis (dirty hack)
                if(substr_count($stack->serialize(), Tokens::PAREN_LEFT) === 0) {
                    throw new \InvalidArgumentException('Parenthesis are misplaced');
                }

                while($stack->top() !== Tokens::PAREN_LEFT) {
                    $queue->enqueue($stack->pop());
                }
            }
            else if(in_array($tokens[$i], Tokens::OPERATORS, true)) {
                while($stack->count() > 0 && in_array($stack->top(), Tokens::OPERATORS)
                    && (($this->isOperatorLeftAssociative($tokens[$i])
                        && $this->getOperatorPrecedence($tokens[$i]) === $this->getOperatorPrecedence($stack->top()))
                    || ($this->getOperatorPrecedence($tokens[$i]) < $this->getOperatorPrecedence($stack->top())))) {
                    $queue->enqueue($stack->pop());
                }

                $stack->push($tokens[$i]);
            }
            else if($tokens[$i] === Tokens::PAREN_LEFT) {
                $stack->push(Tokens::PAREN_LEFT);
            }
            else if($tokens[$i] === Tokens::PAREN_RIGHT) {
                // checking whether stack contains left parenthesis (dirty hack)
                if(substr_count($stack->serialize(), Tokens::PAREN_LEFT) === 0) {
                    throw new \InvalidArgumentException('Parenthesis are misplaced');
                }

                while($stack->top() != Tokens::PAREN_LEFT) {
                    $queue->enqueue($stack->pop());
                }

                $stack->pop();

                if($stack->count() > 0 && array_key_exists($stack->top(), $this->functions)) {
                    $queue->enqueue($stack->pop());
                }
            }
        }

        while($stack->count() > 0) {
            $queue->enqueue($stack->pop());
        }

        return $queue;
    }

    /**
     * Calculates tokens ordered in RPN.
     *
     * @param  \SplQueue $queue
     * @return int|float Result of the calculation.
     * @throws \InvalidArgumentException
     */
    private function calculateFromRPN(\SplQueue $queue) {
        $stack = new \SplStack();

        while($queue->count() > 0) {
            $currentToken = $queue->dequeue();
            if(is_numeric($currentToken)) {
                $stack->push($currentToken);
            }
            else {
                if(in_array($currentToken, Tokens::OPERATORS, true)) {
                    if($stack->count() < 2) {
                        throw new \InvalidArgumentException('Invalid expression');
                    }
                    $stack->push($this->executeOperator($currentToken, $stack->pop(), $stack->pop()));
                }
                else if(array_key_exists($currentToken, $this->functions)) {
                    if($stack->count() < $this->functions[$currentToken]['paramsCount']) {
                        throw new \InvalidArgumentException('Invalid expression');
                    }

                    $params = [];
                    for($i = 0; $i < $this->functions[$currentToken]['paramsCount']; $i++) {
                        $params[] = $stack->pop();
                    }

                    $stack->push($this->executeFunction($currentToken, $params));
                }
            }
        }

        if($stack->count() === 1) {
            return $stack->pop();
        }

        throw new \InvalidArgumentException('Invalid expression');
    }

    /**
     * Calculates the current arithmetic expression.
     *s
     * @param string $expression
     * @return float|int Result of the calculation.
     */
    public function calculate(string $expression) {
        $tokens = $this->tokenizer->tokenize($expression, array_keys($this->functions));
        $rpn    = $this->getReversePolishNotation($tokens);

        return $this->calculateFromRPN($rpn);
    }

    /**
     * @param  string $operator A valid operator.
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function isOperatorLeftAssociative($operator) {
        if(!in_array($operator, Tokens::OPERATORS, true)) {
            throw new \InvalidArgumentException("Cannot check association of $operator operator");
        }

        if($operator === Tokens::POW)
            return false;

        return true;
    }

    /**
     * @param  string $operator A valid operator.
     * @return int
     * @throws \InvalidArgumentException
     */
    private function getOperatorPrecedence($operator) {
        if(!in_array($operator, Tokens::OPERATORS, true)) {
            throw new \InvalidArgumentException("Cannot check precedence of $operator operator");
        }

        if($operator === Tokens::POW) {
            return 6;
        }

        if($operator === Tokens::MULT || $operator === Tokens::DIV) {
            return 4;
        }

        if($operator === Tokens::MOD) {
            return 2;
        }
        return 1;
    }

    /**
     * @param  string    $operator A valid operator.
     * @param  int|float $a First value.
     * @param  int|float $b Second value.
     * @return int|float Result.
     * @throws \InvalidArgumentException
     */
    private function executeOperator($operator, $a, $b) {
        if($operator === Tokens::PLUS) {
            return $a + $b;
        }

        if($operator === Tokens::MINUS) {
            return $b - $a;
        }

        if($operator === Tokens::MOD) {
            return $b % $a;
        }

        if($operator === Tokens::MULT) {
            return $a * $b;
        }

        if($operator === Tokens::DIV) {
            if($a === 0) {
                throw new \InvalidArgumentException('Division by zero occured');
            }
            return $b / $a;
        }

        if($operator === Tokens::POW) {
            return $b ** $a;
        }

        throw new \InvalidArgumentException('Unknown operator provided');
    }

    /**
     * @param  string $functionName
     * @param  array  $params
     * @return int|float Result.
     */
    private function executeFunction($functionName, $params) {
        return call_user_func_array($this->functions[$functionName]['func'], array_reverse($params));
    }
}

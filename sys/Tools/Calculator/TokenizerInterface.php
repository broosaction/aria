<?php
/**
 * Copyright (c) 2019.  Bruce Mubangwa
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: Tsvetan Ovedenski
 * Date: 06/09/2017
 * Time: 11:46
 */

namespace Core\Tools\Calculator;


interface TokenizerInterface
{
    /**
     * @param string $expression
     * @param array $functionNames
     * @return array Tokens of $expression
     */
    public function tokenize(string $expression, array $functionNames = []): array;
}

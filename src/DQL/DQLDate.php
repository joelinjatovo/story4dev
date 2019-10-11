<?php
namespace App\DQL;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode,
    \Doctrine\ORM\Query\SqlWalker,
    \Doctrine\ORM\Query\Parser,
    \Doctrine\ORM\Query\Lexer;

/**
 * Extracts the date form a timestamp (Mysql function)
 * DQLDate :: DATE ( date as Y-m-d )
 */
class DQLDate extends FunctionNode 
{

    protected $date;

    public function getSql(SqlWalker $sqlWalker) 
    {
       return 'DATE(' . $sqlWalker->walkArithmeticPrimary($this->date) .')';
    }

    public function parse(Parser $parser) 
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}

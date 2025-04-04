<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Transformer;

use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Error as PhpParserException;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Translate and print parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Writer
{
    /**
     * @var NodeTraverser Traverser used for altering parsed code
     */
    private $traverser;

    /**
     * @var BracketingPrinter Printer used for printing traversed code
     */
    private $printer;

    /**
     * Optionally inject dependencies
     *
     * Since Reader always makes definitions namespaced a PhpParser printer that
     * wraps the code in brackeded namespace statements must be used. The current
     * implementation of this is BracketingPrinter.
     */
    public function __construct(NodeTraverser $traverser = null, BracketingPrinter $printer = null)
    {
        $this->traverser = $traverser ?: new NodeTraverser;
        $this->printer = $printer ?: new BracketingPrinter;
    }

    /**
     * Apply translation to alter code
     */
    public function apply(NodeVisitor $translation): self
    {
        $this->traverser->addVisitor($translation);
        return $this;
    }

    /**
     * Generate new code snippet
     *
     * @param Namespace_[] $statements
     * @return string
     */
    public function write(array $statements): string
    {
        try {
            return $this->printer->prettyPrint(
                $this->traverser->traverse(
                    $statements
                )
            );
        } catch (PhpParserException $e) {
            throw new RuntimeException("Error generating code: {$e->getMessage()}", 0, $e);
        }
    }
}

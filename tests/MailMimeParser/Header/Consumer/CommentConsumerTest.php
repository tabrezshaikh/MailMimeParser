<?php

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\PartFactory;
use ZBateson\MailMimeParser\Header\Consumer\CommentConsumer;

/**
 * Description of CommentConsumerTest
 *
 * @group Consumers
 * @group CommentConsumer
 * @author Zaahid Bateson
 */
class CommentConsumerTest extends PHPUnit_Framework_TestCase
{
    private $commentConsumer;
    
    public function setUp()
    {
        $pf = new PartFactory();
        $cs = new ConsumerService($pf);
        $this->commentConsumer = CommentConsumer::getInstance($cs, $pf);
    }
    
    public function tearDown()
    {
        unset($this->commentConsumer);
    }
    
    protected function assertCommentConsumed($expected, $value)
    {
        $ret = $this->commentConsumer->__invoke($value);
        $this->assertNotEmpty($ret);
        $this->assertCount(1, $ret);
        $this->assertInstanceOf('\ZBateson\MailMimeParser\Header\Part\CommentPart', $ret[0]);
        $this->assertEquals('', $ret[0]->getValue());
        $this->assertEquals($expected, $ret[0]->getComment());
    }
    
    public function testConsumeTokens()
    {
        $comment = 'Some silly comment made about my moustache';
        $this->assertCommentConsumed($comment, $comment);
    }
    
    public function testNestedComments()
    {
        $comment = 'A very silly comment (made about my (very awesome) moustache no less)';
        $this->assertCommentConsumed($comment, $comment);
    }
    
    public function testCommentWithQuotedLiteral()
    {
        $comment = 'A ("very ) wrong") comment was made (about my moustache obviously)';
        $this->assertCommentConsumed($comment, $comment);
    }
    
    public function testMimeEncodedComment()
    {
        $this->assertCommentConsumed(
            'A comment was made (about my moustache obviously)',
            'A comment was made (about my =?ISO-8859-1?Q?moustache?= obviously)'
        );
    }
}

<?php

namespace IDF\RealEmailValidation\Tests\Rules;

use Illuminate\Contracts\Validation\Rule;
use IDF\RealEmailValidation\Tests\TestCase;
use IDF\RealEmailValidation\Rules\RealEmail;

/**
 * Inspired by
 * https://github.com/symfony/validator/blob/4.4/Tests/Constraints/EmailValidatorTest.php.
 */
class RealEmailTest extends TestCase
{
    /**
     * @test
     * @dataProvider validEmailProvider
     * @group network
     */
    public function it_passes_on_valid_email($validEmail)
    {
        $rule = new RealEmail(['html5', 'rfc', 'host'], false);

        $this->assertTrue($rule->passes('random_input_name', $validEmail), $this->getErrorMessages($rule));
    }

    /**
     * @test
     * @dataProvider invalidEmailsForHostCheckProvider
     * @group network
     */
    public function it_fails_on_invalid_host($invalidEmail)
    {
        $rule = new RealEmail(['host']);

        $this->assertFalse($rule->passes('random_input_name', $invalidEmail));
    }

    /**
     * @test
     * @dataProvider invalidEmailsForMissingHostCheckProvider
     * @group network
     */
    public function it_fails_on_missing_host($invalidEmail)
    {
        $rule = new RealEmail(['host']);

        $this->assertFalse($rule->passes('random_input_name', $invalidEmail));
    }

    /**
     * @test
     * @dataProvider invalidEmailForRfcCheckProvider
     */
    public function it_fails_by_rfc_check($invalidEmail)
    {
        $rule = new RealEmail(['rfc']);

        $this->assertFalse($rule->passes('random_input_name', $invalidEmail), $invalidEmail);
    }

    /**
     * @test
     * @dataProvider invalidEmailsForHtml5CheckProvider
     */
    public function it_fails_by_html5_check($invalidEmail)
    {
        $rule = new RealEmail(['html5']);

        $this->assertFalse($rule->passes('random_input_name', $invalidEmail), $invalidEmail);
    }

    public function validEmailProvider()
    {
        return [
            ['example@gmail.com'],
            ['example@yahoo.com'],
            ['example@example.co.uk'],
        ];
    }

    public function invalidEmailsForHostCheckProvider()
    {
        return [
            ['example@gmail.con'],
            ['example@gmail.ocm'],
        ];
    }

    public function invalidEmailsForMissingHostCheckProvider()
    {
        return [
            ['example'],
            ['example@'],
            ['gmail.com'],
        ];
    }

    public function invalidEmailForRfcCheckProvider()
    {
        return [
            ['test@example.com test'],
            ['user  name@example.com'],
            ['user   name@example.com'],
            ['example.@example.co.uk'],
            ['example@example@example.co.uk'],
            ['(test_exampel@example.fr)'],
            ['example(example)example@example.co.uk'],
            ['.example@localhost'],
            ['ex\ample@localhost'],
            ['example@local\host'],
            ['example@localhost.'],
            ['user name@example.com'],
            ['username@ example . com'],
            ['example@(fake).com'],
            ['example@(fake.com'],
            ['username@example,com'],
            ['usern,ame@example.com'],
            ['user[na]me@example.com'],
            ['"""@iana.org'],
            ['"\"@iana.org'],
            ['"test"test@iana.org'],
            ['"test""test"@iana.org'],
            ['"test"."test"@iana.org'],
            ['"test".test@iana.org'],
            ['"test"'.\chr(0).'@iana.org'],
            ['"test\"@iana.org'],
            [\chr(226).'@iana.org'],
            ['test@'.\chr(226).'.org'],
            ['\r\ntest@iana.org'],
            ['\r\n test@iana.org'],
            ['\r\n \r\ntest@iana.org'],
            ['\r\n \r\ntest@iana.org'],
            ['\r\n \r\n test@iana.org'],
            ['test@iana.org \r\n'],
            ['test@iana.org \r\n '],
            ['test@iana.org \r\n \r\n'],
            ['test@iana.org \r\n\r\n'],
            ['test@iana.org  \r\n\r\n '],
            ['test@iana/icann.org'],
            ['test@foo;bar.com'],
            ['test;123@foobar.com'],
            ['test@example..com'],
            ['email.email@email."'],
            ['test@email>'],
            ['test@email<'],
            ['test@email{'],
            [str_repeat('x', 254).'@example.com'], //email with warnings
        ];
    }

    public function invalidEmailsForHtml5CheckProvider()
    {
        return [
            ['example'],
            ['example@'],
            ['example@localhost'],
            ['example@example.co..uk'],
            ['foo@example.com bar'],
            ['example@example.'],
            ['example@.fr'],
            ['@example.com'],
            ['example@example.com;example@example.com'],
            ['example@.'],
            [' example@example.com'],
            ['example@ '],
            [' example@example.com '],
            [' example @example .com '],
            ['example@-example.com'],
            [sprintf('example@%s.com', str_repeat('a', 64))],
        ];
    }

    private function getErrorMessages(Rule $rule): string
    {
        return json_encode($rule->message());
    }
}

<?php

declare(strict_types=1);

namespace IDF\RealEmailValidation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;

/**
 * Inspired by
 * https://github.com/symfony/symfony/blob/4.4/src/Symfony/Component/Validator/Constraints/EmailValidator.php.
 */
final class RealEmail implements Rule
{
    /** Check against RFC 5321, 5322, 6530, 6531, 6532 (no warnings allowed) */
    public const CONSTRAIN_STRICT_RFC = 'rfc';

    /** Check DNS Records for the extracted host */
    public const CONSTRAIN_HOST = 'host';

    /** Check DNS Records for MX type */
    public const CONSTRAIN_MX = 'mx';

    /**
     * Matches the pattern used for the HTML5 email input element.
     * @see https://html.spec.whatwg.org/multipage/forms.html#email-state-typeemail
     */
    public const CONSTRAIN_HTML5 = 'html5';

    /** @see https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address */
    private const PATTERN_HTML5 = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    private $constraints = [];
    private $messages = [];
    private $bail;

    public function __construct(array $constraints = ['html5', 'rfc', 'host'], bool $bail = true)
    {
        $this->constraints = $constraints;
        $this->bail = $bail;
    }

    /** {@inheritdoc} */
    public function passes($attribute, $email): bool
    {
        $this->messages = [];

        if ($this->hasConstraint('html5') && ! $this->checkHtml5($email)) {
            $this->messages[] = __('realEmailValidation::messages.html5');
            if ($this->bail) {
                return false;
            }
        }

        if ($this->hasConstraint('rfc')) {
            $rfcValidator = new EmailValidator();
            $hasHtml5Error = ! $rfcValidator->isValid($email, new NoRFCWarningsValidation());
            if ($hasHtml5Error) {
                $this->messages[] = __('realEmailValidation::messages.rfc');
                if ($this->bail) {
                    return false;
                }
            }
        }

        $hostSeparatorPosition = strrpos($email, '@');
        if ($hostSeparatorPosition === FALSE || $hostSeparatorPosition === strlen($email) - 1) {
            $this->messages[] = __('realEmailValidation::messages.nohost');
            if ($this->bail) {
                return false;
            }
        } else {
            $emailTail = (string) substr($email, $hostSeparatorPosition + 1);
            $host = idn_to_ascii($emailTail);
            if (substr($host, -1) !== '.') {
                $host .= '.';
            }

            if ($this->hasConstraint('mx') && ! $this->checkMX($host)) {
                $this->messages[] = __('realEmailValidation::messages.mx');
                if ($this->bail) {
                    return false;
                }
            }

            if ($this->hasConstraint('host') && ! $this->checkHost($host)) {
                $this->messages[] = __('realEmailValidation::messages.host');
                if ($this->bail) {
                    return false;
                }
            }
        }

        return count($this->messages) === 0;
    }

    /** {@inheritdoc} */
    public function message(): array
    {
        return $this->messages;
    }

    private function hasConstraint(string $constrain): bool
    {
        return in_array($constrain, $this->constraints, true);
    }

    /**
     * Check against HTML5 pattern.
     */
    private function checkHtml5(string $email): bool
    {
        return (bool) preg_match(self::PATTERN_HTML5, $email);
    }

    /**
     * Check DNS Records for MX type.
     * ⚠ This option is not reliable because it depends on the network conditions
     * and some valid servers refuse to respond to those requests.
     */
    private function checkMX(string $host): bool
    {
        return checkdnsrr($host, 'MX');
    }

    /**
     * Check if one of MX, A or AAAA DNS RR exists.
     */
    private function checkHost(string $host): bool
    {
        return checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || $this->checkMX($host);
    }
}

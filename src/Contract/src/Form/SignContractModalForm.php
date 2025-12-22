<?php
declare(strict_types = 1);
namespace Frontend\Contract\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Validator\EmailAddress;

class SignContractModalForm extends AbstractContractForm
{

    public $num_emails = 0;

    public function __construct(string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->setAttribute('id', 'sign-contract-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
    }

    public function init(): void
    {
        parent::init();

        for ($i = 0; $i < $this->num_emails; $i ++) {
            $email = new Text();
            $email->setAttributes([
                'class' => 'form-control'
            ])->setOptions([
                'label' => 'Email'
            ]);
            $email->setName("EMAIL_$i");
            $this->add($email);
        }
    }

    public function getInputFilterSpecification(): array
    {
        $spec = [];

        for ($i = 0; $i < $this->num_emails; $i ++) {
            $key = "EMAIL_$i";

            $spec[$key] = [

                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class
                    ],
                    [
                        'name' => StripTags::class
                    ]
                ],
                'validators' => [
                    [
                        'name' => EmailAddress::class,
                        'options' => [
                            // 'allow' => EmailAddress::ALLOW_DNS, // default: ALLOW_ALL; using ALLOW_DNS enforces domain checks

                            // Validate using DNS (MX/A) records; can be EmailAddress::CHECK_DNS or EmailAddress::CHECK_HOSTNAME
                            'useMxCheck' => true, // best-effort DNS MX check (may slow validation)
                            'useDeepMxCheck' => false // deeper MX check (slower, optional)

                        // 'hostnameValidator' options can be provided if needed
                        // 'hostnameValidator' => [
                        // 'allow' => Hostname::ALLOW_DNS,
                        // ],
                        // Set custom message templates or disable domain-literal validation, etc.
                        ]
                    ],

                    // Optional: add a Regex validator for additional constraints
                    [
                        'name' => \Laminas\Validator\Regex::class,
                        'options' => [
                            'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'
                        ]
                    ]
                ]
            ];
        }

        return $spec;
    }
}
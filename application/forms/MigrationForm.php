<?php

/* Icinga Web 2 | (c) 2023 Icinga GmbH | GPLv2+ */

namespace Icinga\Forms;

use ipl\Html\Form;
use ipl\Web\Common\CsrfCounterMeasure;
use ipl\Web\Common\FormUid;

class MigrationForm extends Form
{
    use CsrfCounterMeasure;
    use FormUid;

    protected $defaultAttributes = [
        'class' => ['icinga-form', 'migration-form', 'icinga-controls'],
        'name'  => 'migration-form'
    ];

    public function hasBeenSubmitted(): bool
    {
        if (! $this->hasBeenSent()) {
            return false;
        }

        $pressedButton = $this->getPressedSubmitElement();
        return $pressedButton && strpos($pressedButton->getName(), 'migrate-') !== false;
    }

    protected function assemble(): void
    {
        $this->addHtml($this->createUidElement());
    }
}

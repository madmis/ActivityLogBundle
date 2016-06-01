Symfony ActivityLog Component
==================================

[![SensioLabsInsight][sensiolabs-insight-image]][sensiolabs-insight-link]
[![Build Status][testing-image]][testing-link]
[![Latest Stable Version][stable-image]][package-link]
[![Total Downloads][downloads-image]][package-link]

[![License][license-image]][license-link]

ActivityLogBundle - Extended doctrine loggable (StofDoctrineExtensionsBundle)

What's inside
------------

ActivityLogBundle uses **Loggable** extension from [StofDoctrineExtensionsBundle][stof-link] and [DoctrineExtensions][doctrine-link]

This bundle extend **Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry** with below fields:

- parentId - store depedency to "main entity"
- parentClass - store "main entity" type
- oldData - data that were changed
- name - entry name (to show in activity log)
- user - associations mapping with user who changed data

Bundle contain extended listener (**LoggableListener**) to process above fields.

Also available formatter to preprocessing activity log before show in view (html). 


Installation
------------
Pretty simple with Composer, run:

``` bash
composer require madmis/activity-log-bundle
```

Then enable the bundle in the kernel:

``` php
public function registerBundles()
{
    $bundles = [
        // ...
        new ActivityLogBundle\ActivityLogBundle(),
        // ...
    ];
    ...
}
```

Configure bundle:

``` yml
# app/config/config.yml
doctrine:
    dbal:
        #...
    orm:
        #...
        resolve_target_entities:
            Symfony\Component\Security\Core\User\UserInterface: AppBundle\Entity\User
        mappings:
            gedmo_loggable:
                type: annotation
                prefix: Gedmo\Loggable\Entity
                dir: "%kernel.root_dir%/../src/AppBundle/Entity/"
                alias: GedmoLoggable
                is_bundle: false

stof_doctrine_extensions:
    class:
        loggable: ActivityLogBundle\Listener\LoggableListener
    orm:
        default:
            loggable: true
            
activity_log:
    # namespace prefix for custom formatters 
    formatter_prefix: "AppBundle\\Service\\ActivityFormatter"
```

Create entity and make it loggable:

```php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;

/**
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="ProjectRepository")
 * @ORM\Table
 * @Gedmo\Loggable(logEntryClass="ActivityLogBundle\Entity\LogEntry")
 */
class Project implements StringableInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     * @Gedmo\Versioned
     */
    private $key;

    //...
```
**StringableInterface** required to save **LogEntry::name**.

Then run command to update database schema:

``` bash
php bin/console doctrine:schema:update --force
```

Using formatter to data view
------------

Formatter class: **ActivityLogBundle\Service\ActivityLog\ActivityLogFormatter**
Formatter service: **activity_log.formatter**

required: **LoggerInterface**, **EntityManager** and **formatter_prefix** parameter as dependencies

By default entity without custom formatter class formatted by **ActivityLogBundle\Service\ActivityLog\EntityFormatter\UniversalFormatter**

But you can implement custom formatter for each entity.

As example formatter for **AppBundle\Entity\Project** entity:

```php

namespace AppBundle\Service\ActivityFormatter;

class Project extends AbstractFormatter implements FormatterInterface
{
    /**
     * @param LogEntry $log
     * @return array
     */
    public function format(LogEntry $log)
    {
        $result = $log->toArray();

        if ($log->isCreate()) {
            $result['message'] = sprintf('The <b>Project <span class="font-green-jungle">"%s"</span></b> was created.', $log->getName());
        } else if ($log->isRemove()) {
            $result['message'] = sprintf('The <b>Project <span class="font-red-flamingo">"%s"</span></b> was removed.', $log->getName());
        } else if ($log->isUpdate()) {
            $result['message'] = '<dl><dt>The <b>Project <span class="font-yellow-gold">"%s"</span></b> was updated.</dt>%s</dl>';
            $data = $log->getData();
            $oldData = $log->getOldData();

            $text = '';
            foreach ($data as $field => $value) {
                $value = $this->normalizeValue($field, $value);

                if (array_key_exists($field, $oldData)) {
                    $oldValue = $this->normalizeValue($field, $oldData[$field]);
                    $subText = sprintf('from "<b>%s</b>" to "<b>%s</b>".', $oldValue, $value);
                } else {
                    $subText = sprintf('to "<b>%s</b>".', $value);
                }
                $text .= sprintf('<dd>Property "<b>%s</b>" was changed: %s</dd>', $field, $subText);
            }

            $result['message'] = sprintf($result['message'], $log->getName(), $text);
        } else {
            $result['message'] = "Undefined action: {$log->getAction()}.";
        }

        return $result;
    }
}
```

If entity has association with other entity it can be resolved by  **AbstractFormatter::normalizeValue**.
This method call method from the entity formatter class, which named as appropriate property.

For example, **Project** entity has association mapping **ManyToOne** to **Type** entity. 
To get **Type** name we can add method **type** to **Project** formatter:

```php
namespace AppBundle\Service\ActivityFormatter;

class Project extends AbstractFormatter implements FormatterInterface
{
    //...
    
    /**
     * @param array $value
     * @return string
     */
    protected function type(array $value)
    {
        if (isset($value['id'])) {
            /** @var Type $entity */
            $entity = $this->entityManager->getRepository('AppBundle:Type')
                ->find($value['id']);

            if ($entity) {
                return $entity->getName();
            }
        }

        return '';
    }
```

As result we have formatted response to show in view

[sensiolabs-insight-link]: https://insight.sensiolabs.com/projects/9b7eb683-a440-4f68-804a-38ae107e75d0
[sensiolabs-insight-image]: https://insight.sensiolabs.com/projects/9b7eb683-a440-4f68-804a-38ae107e75d0/mini.png

[package-link]: https://packagist.org/packages/madmis/activity-log-bundle
[downloads-image]: https://poser.pugx.org/madmis/activity-log-bundle/downloads
[stable-image]: https://poser.pugx.org/madmis/activity-log-bundle/v/stable
[license-image]: https://poser.pugx.org/madmis/activity-log-bundle/license
[license-link]: https://packagist.org/packages/madmis/activity-log-bundle

[testing-link]: https://travis-ci.org/madmis/ActivityLogBundle
[testing-image]: https://travis-ci.org/madmis/ActivityLogBundle.svg?branch=master

[stof-link]: https://github.com/stof/StofDoctrineExtensionsBundle
[doctrine-link]: https://github.com/Atlantic18/DoctrineExtensions


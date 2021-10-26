<?php

declare(strict_types=1);

namespace JmvDevelop\MediaBundle\Graphql;

use JmvDevelop\GraphqlGenerator\Schema\Argument;
use JmvDevelop\GraphqlGenerator\Schema\EnumType;
use JmvDevelop\GraphqlGenerator\Schema\ObjectField;
use JmvDevelop\GraphqlGenerator\Schema\ObjectType;
use JmvDevelop\GraphqlGenerator\Schema\QueryField;
use JmvDevelop\GraphqlGenerator\Schema\SchemaDefinition;
use JmvDevelop\GraphqlGenerator\SchemaGenerator\ObjectField\AbstractObjectFieldGenerator;
use JmvDevelop\GraphqlGenerator\SchemaGenerator\ObjectField\CallbackObjectFieldGenerator;
use JmvDevelop\MediaBundle\Entity\Media;
use Nette\PhpGenerator\Method;

final class GraphqlConfig
{
    public function __construct(
        private SchemaDefinition $schema,
    ) {
    }

    /**
     * @param list<string> $filters
     */
    public function addFilterEnum(array $filters): void
    {
        $this->schema->addType(EnumType::create(
            name: 'ImageFilterEnum',
            values: \array_map(function (string $filter) {
            return EnumType::value(name: $filter, value: $filter);
        }, $filters),
        ));
    }

    public function addTypes(): void
    {
        $assertIntNotNull = new CallbackObjectFieldGenerator(function (ObjectType $type, ObjectField $field, Method $method): void {
            $method->addBody(\sprintf('
            $v = $root->get%s();
            if (!is_int($v)) {
                throw new \RuntimeException("must be int");
            }
            return $v;
            ', \ucfirst($field->getName())))->setFinal();
        });

        $abstract = new AbstractObjectFieldGenerator();

        $this->schema->addType(ObjectType::create(
            name: 'Image',
            rootType: '\\'.Media::class,
            fields: [
                ObjectType::field(name: 'id', type: 'Int!', generator: $assertIntNotNull),
                ObjectType::field(name: 'name', type: 'String!'),
                ObjectType::field(name: 'context', type: 'String!'),
                ObjectType::field(name: 'width', type: 'Int!'),
                ObjectType::field(name: 'height', type: 'Int!'),
                ObjectType::field(name: 'createdDate', type: 'DateTimeTz!'),
                ObjectType::field(name: 'referenceUrl', type: 'String!', generator: $abstract),
                ObjectType::field(name: 'thumbnailUrl', type: 'String!', generator: $abstract),
                ObjectType::field(name: 'filteredUrl', type: 'String!', args: [
                    Argument::create(name: 'filter', type: 'ImageFilterEnum!'),
                ]),
            ]
        ));
    }

    public function addFields(): void
    {
        $this->schema->addQueryField(QueryField::create(name: 'image', type: 'Image', args: [
            Argument::create(name: 'id', type: 'Int!'),
        ]));

        $this->schema->addQueryField(QueryField::create(name: 'strictImage', type: 'Image!', args: [
            Argument::create(name: 'id', type: 'Int!'),
        ]));
    }
}

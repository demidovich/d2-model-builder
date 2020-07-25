<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use D2\Entity\EntityBuilder;
use Exception;
use Tests\Stub\Model;
use Tests\Stub\ModelAddress;
use Tests\Stub\ModelId;

class EntityBuilderTest extends TestCase
{
    private $params = [
        'id' => 100,
        'primitive_id' => 200,
        'primitive_string' => 'string'
    ];

    public function test_constructor_by_array()
    {
        $model = EntityBuilder::byConstructor(Model::class, $this->params);

        $this->instance_asserts($model, $this->params);
    }

    public function test_constructor_by_object()
    {
        $model = EntityBuilder::byConstructor(Model::class, (object) $this->params);

        $this->instance_asserts($model, $this->params);
    }

    public function test_static_constructor_by_array()
    {
        $model = EntityBuilder::byStaticConstructor(Model::class, 'create', $this->params);

        $this->instance_asserts($model, $this->params);
    }

    public function test_static_constructor_by_object()
    {
        $model = EntityBuilder::byStaticConstructor(Model::class, 'create', (object) $this->params);

        $this->instance_asserts($model, $this->params);
    }

    private function instance_asserts(Model $instance, $params)
    {
        $id = ModelId::fromPrimitive($params['id']);

        $this->assertNotEmpty($instance->id());
        $this->assertInstanceOf(ModelId::class, $instance->id());
        $this->assertTrue($instance->id()->equalsTo($id));

        $primitiveId = $params['primitive_id'];

        $this->assertNotEmpty($instance->primitiveId());
        $this->assertEquals($primitiveId, $instance->primitiveId());

        $primitiveString = $params['primitive_string'];

        $this->assertNotEmpty($instance->primitiveString());
        $this->assertEquals('string', $primitiveString);

        $this->assertNull($instance->nullablePrimitiveId());
        $this->assertNull($instance->nullableAddress());
    }

    public function test_constructor_value_object()
    {
        $id = ModelId::fromPrimitive(100);

        $model = EntityBuilder::byConstructor(Model::class, [
            'id' => $id,
            'primitive_id' => 200,
            'primitive_string' => 'string'
        ]);

        $this->assertInstanceOf(ModelId::class, $model->id());
        $this->assertTrue($model->id()->equalsTo($id));
    }

    public function test_constructor_prefix()
    {
        $params = [
            'address_city'   => 'Moscow',
            'address_street' => 'Krasnaya',
        ];

        $address = EntityBuilder::byConstructor(ModelAddress::class, $params, 'address');

        $this->assertInstanceOf(ModelAddress::class, $address);
        $this->assertEquals($params['address_city'], $address->city());
        $this->assertEquals($params['address_street'], $address->street());
    }

    public function test_missing_primitive_param_exception()
    {
        $this->expectException(Exception::class);

        $params = [
            'id' => 100,
            'primitive_id___' => 200,
            'primitive_string' => 'string'
        ];

        EntityBuilder::byConstructor(Model::class, $params);
    }

    public function test_missing_value_object_param_exception()
    {
        $this->expectException(Exception::class);

        $params = [
            'id___' => 100,
            'primitive_id' => 200,
            'primitive_string' => 'string'
        ];

        EntityBuilder::byConstructor(Model::class, $params);
    }
}
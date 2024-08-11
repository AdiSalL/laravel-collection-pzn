<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Data\Person;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection() {
        $collection = collect([1,2,3]);
        self::assertEqualsCanonicalizing([1,2,3], $collection->all());
    }

    public function testForEach() {
        $collection = collect([1,2,3,4,5,6,7,8]);
        foreach($collection as $key => $collect) {

            $this->assertEquals($key + 1, $collect);
        }
    }

    public function testCrud() {
        $collection = collect([]);
        $collection->push(1, 2, 3);

        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());

        $result = $collection->pop();
        self::assertEquals($result, 3);
        self::assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap() {
        $collection = collect([1,2,3]);
        $result = $collection->map(function ($collect) {
            return $collect * 2;
        });
        $this->assertEqualsCanonicalizing([2,4,6], $result->all());
    }

    public function testMapInto() {
        $collection = collect(["Eko"]);
        $result = $collection->mapInto(Person::class);
        self::assertEquals([new Person("Eko")], $result->all());
    }

    public function testMapSpread() 
    {
        $collection = collect([
            ["Adi", "Salafudin"],
            ["Ida", "Lafudin"]
        ]);

        $result = $collection->mapSpread(function ($first, $last) {
            $fullName = $first . " " .  $last;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Adi Salafudin"),
            new Person("Ida Lafudin"),
        ], $result->all());
    }

    public function testMapToGroups() {
        $collection = collect([
            [
            "name" => "Adi",
            "department" => "IT"
            ],  
            [
            "name" => "Salafudin",
            "department" => "IT"
            ],
            [
            "name" => "Eko",
            "department" => "Design"
            ],
            [
            "name" => "Kurniawan",
            "department" => "Design"
            ],
            [
            "name" => "Khannedy",
            "department" => "HR"
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["department"] => $person["name"]
            ];
        });
        var_dump($result);
        self::assertEquals([
            "IT" => collect(["Adi", "Salafudin"]),
            "Design" => collect(["Eko", "Kurniawan"]),
            "HR" => collect(["Khannedy"])
        ], $result->all());
    }


}

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

        self::assertEquals([
            "IT" => collect(["Adi", "Salafudin"]),
            "Design" => collect(["Eko", "Kurniawan"]),
            "HR" => collect(["Khannedy"])
        ], $result->all());
    }

    public function testZip() {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);
        
        $this->assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6]),
        ],$collection3->all());
    
    }

    public function testConcat() {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);
        
        $this->assertEquals([1,2,3,4,5,6],$collection3->all());
    }

    public function testCombine() {
        $collection1 = collect(["name", "country"]);
        $collection2 = ["Adi", "Indonesia"];
        $collection3 = $collection1->combine($collection2);
        
        $this->assertEquals([
            "name" => "Adi",
            "country" => "Indonesia"
        ],$collection3->all());
    }

    public function testCollapse() {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9],
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());
    }

    public function testFlatMap() {
        $collection = collect([
            [
                "name" => "Adi",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Eko",
                "hobbies" => ["Reading", "Writing"]
            ],
        ]);
        $result = $collection->flatMap(function($item) {
            $hobbies = $item["hobbies"];
            return $hobbies;
        });
        $this->assertEqualsCanonicalizing(["Coding", "Gaming", "Reading", "Writing"], $result->all());
    }

    public function testStringRepresentation() {
        $collection = collect(["Eko", "Kurniawan", "Khannedy"]);
        
        $this->assertEquals("Eko-Kurniawan-Khannedy", $collection->join("-"));
        $this->assertEquals("Eko-Kurniawan_Khannedy", $collection->join("-", "_"));
        $this->assertEquals("Eko, Kurniawan and Khannedy", $collection->join(", ", " and "));
        
    }

    public function testFilter() {
        $collection = collect([
            "Adi" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        
        $result = $collection->filter(function($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            "Adi" => 100,
            "Joko" => 90
        ], $result->all());
    }

    public function testFilterIndex() {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key) {
            return  $value % 2 == 0;
        });
        $this->assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }

    public function testPartion() {
        $collection = collect([
            "Adi" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        
        [$result1, $result2] = $collection->partition(function($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            "Adi" => 100,
            "Joko" => 90
        ], $result1->all());
        $this->assertEquals([
            "Budi" => 80
        ], $result2->all());
    }

    public function testTesting() {
        $collection = collect(["Eko", "Kurniawan", "Khannedy"]);
        $this->assertTrue($collection->contains("Eko"));
        $this->assertTrue($collection->contains(function ($value, $key){
            return $value == "Khannedy";
        }));
        
    }
}

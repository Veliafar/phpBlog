<?php

namespace Veliafar\PhpBlog\Blog\Commands;

use PHPUnit\Framework\TestCase;
use Veliafar\PhpBlog\Blog\Exceptions\ArgumentsException;

class ArgumentsTest extends TestCase
{

  /**
   * @throws ArgumentsException
   */
  public function testItReturnArgumentsValueByName(): void
  {
    $arguments = new Arguments(['some_key' => 123]);
    $value = $arguments->get('some_key');

    $this->assertSame('123', $value);
    $this->assertIsString($value);
  }

  public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
  {
    $arguments = new Arguments([]);
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage("No such argument: some_key");
    $arguments->get('some_key');
  }

  // Провайдер данных
  public function argumentsProvider(): iterable
  {
    return [
      ['some_string', 'some_string'], // Тестовый набор
      // Первое значение будет передано
      // в тест первым аргументом,
      // второе значение будет передано
      // в тест вторым аргументом
      [' some_string', 'some_string'], // Тестовый набор №2
      [' some_string ', 'some_string'],
      [123, '123'],
      [12.3, '12.3'],
    ];
  }

  /**
   * @dataProvider argumentsProvider
   * @throws ArgumentsException
   */
  public function testItConvertsArgumentsToStrings(
    $inputValue,
    $expectedValue
  ): void {
    // Подставляем первое значение из тестового набора
    $arguments = new Arguments(['some_key' => $inputValue]);
    $value = $arguments->get('some_key');
    // Сверяем со вторым значением из тестового набора
    $this->assertEquals($expectedValue, $value);
  }

}
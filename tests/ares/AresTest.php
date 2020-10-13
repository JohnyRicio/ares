<?php declare(strict_types = 1);

namespace registryAres\tests\ares;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use registryAres\src\Ares\Ares;
use RuntimeException;
use TypeError;

final class AresTest extends TestCase
{

    public function testErrorCompanyIdByInt(): void
    {
        $this->expectException(TypeError::class);
        $ares = $this->_getAres();
        $ares->getByCompanyId(123);
    }

    public function testErrorCompanyIdByNull(): void
    {
        $this->expectException(TypeError::class);
        $ares = $this->_getAres();
        $ares->getByCompanyId(NULL);
    }

    public function testErrorCompanyIdByArray(): void
    {
        $this->expectException(TypeError::class);
        $ares = $this->_getAres();
        $ares->getByCompanyId([]);
    }

    public function testErrorCompanyIdByBool(): void
    {
        $this->expectException(TypeError::class);
        $ares = $this->_getAres();
        $ares->getByCompanyId(FALSE);
    }

    public function testErrorCompanyIdByDateTime(): void
    {
        $this->expectException(TypeError::class);
        $ares = $this->_getAres();
        $ares->getByCompanyId(new DateTime());
    }

    public function testErrorCompanyIdByInvalidString(): void
    {
        $this->expectException(TypeError::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('asdgvcfg');
    }

    public function testErrorCompanyIdByShortId(): void
    {
        $this->expectException(TypeError::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('1231231');
    }

    public function testErrorCompanyIdByLongId(): void
    {
        $this->expectException(TypeError::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('123123123');
    }

    public function testGetCorrectResult(): void
    {
        $ares = $this->_getAres();
        $dataAres = $ares->getByCompanyId('48136000');
        self::assertSame('Hrad I. nádvoří', $dataAres->street);
        self::assertSame('', $dataAres->vatNumber);
        self::assertSame('1', $dataAres->streetNo1);
        self::assertSame('', $dataAres->streetNo2);
        self::assertSame('11900', $dataAres->zip);
        self::assertSame('Kancelář prezidenta republiky', $dataAres->company);
        self::assertTrue($dataAres->state);
        self::assertSame('Praha', $dataAres->city);
        self::assertSame('48136000', $dataAres->companyId);
        self::assertSame('Česká republika', $dataAres->country);
        self::assertSame('Hradčany', $dataAres->partOfCity);
    }

    public function testGetCorrectResultWithDIC(): void
    {
        $ares = $this->_getAres();
        $dataAres = $ares->getByCompanyId('00075370');
        self::assertSame('náměstí Republiky', $dataAres->street);
        self::assertSame('CZ00075370', $dataAres->vatNumber);
        self::assertSame('1', $dataAres->streetNo1);
        self::assertSame('1', $dataAres->streetNo2);
        self::assertSame('30100', $dataAres->zip);
        self::assertSame('Statutární město Plzeň', $dataAres->company);
        self::assertTrue($dataAres->state);
        self::assertSame('Plzeň', $dataAres->city);
        self::assertSame('00075370', $dataAres->companyId);
        self::assertSame('Česká republika', $dataAres->country);
        self::assertSame('Vnitřní Město', $dataAres->partOfCity);
    }

    public function testGetIncorrectResult(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectErrorMessageMatches('/Problem in ARES: .*/');
        $ares = $this->_getAres();
        $ares->getByCompanyId('11111111');
    }

    public function testStatusError(): void
    {
    	$client = $this->createMock(Client::class);
    	$client->method('request')->willReturn(new Response(404));

    	$ares = $this->_getAres($client);

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Problem with connection');
        $ares->getByCompanyId('48136000');
    }

    public function testNoData(): void
	{
		$client = $this->createMock(Client::class);
		$client->method('request')->willReturn(new Response(200));

		$ares = $this->_getAres($client);

		$this->expectException(RuntimeException::class);
		$this->expectErrorMessage('Problem with xml parser');
		$ares->getByCompanyId('48136000');
	}

    public function testReturnedBadData(): void
	{
		$client = $this->createMock(Client::class);
		$client->method('request')->willReturn(new Response(200, [], file_get_contents(__DIR__.'/data/FakeData.xml')));

		$ares = $this->_getAres($client);

		$this->expectException(RuntimeException::class);
		$this->expectErrorMessage('Returned data are bad');

		$ares->getByCompanyId('48136000');
	}

	private function _getAres(?MockObject $stubClient = NULL): Ares {
    	return new Ares($stubClient ?: new Client());
	}

}

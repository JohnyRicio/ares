<?php declare(strict_types = 1);

namespace RegistryAres\Tests\Ares;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RegistryAres\Ares\Ares;
use RegistryAres\Ares\Exception\ExternalAresException;
use RegistryAres\Ares\Exception\IncorrectReturnedDataException;
use RegistryAres\Ares\Exception\InvalidArgumentException;
use Throwable;

final class AresTest extends TestCase
{

    public function testErrorCompanyIdByInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('asdgvcfg');
    }

    public function testErrorCompanyIdByShortId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('1231231');
    }

    public function testErrorCompanyIdByLongId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Company id must be 8 integers');
        $ares = $this->_getAres();
        $ares->getByCompanyId('123123123');
    }

    public function testGetCorrectResult(): void
    {
        $ares = $this->_getAres();
        $dataAres = $ares->getByCompanyId('48136000');
        self::assertSame('Hrad I. nádvoří', $dataAres->address->street);
        self::assertSame('', $dataAres->vatNumber);
        self::assertSame('1', $dataAres->address->streetNo1);
        self::assertSame('', $dataAres->address->streetNo2);
        self::assertSame('11900', $dataAres->address->zip);
        self::assertSame('Kancelář prezidenta republiky', $dataAres->companyName);
        self::assertSame('Praha', $dataAres->address->city);
        self::assertSame('48136000', $dataAres->companyId);
        self::assertSame('Česká republika', $dataAres->address->country);
        self::assertSame('Hradčany', $dataAres->address->district);
    }

    public function testGetCorrectResultWithDIC(): void
    {
        $ares = $this->_getAres();
        $dataAres = $ares->getByCompanyId('00075370');
        self::assertSame('náměstí Republiky', $dataAres->address->street);
        self::assertSame('CZ00075370', $dataAres->vatNumber);
        self::assertSame('1', $dataAres->address->streetNo1);
        self::assertSame('1', $dataAres->address->streetNo2);
        self::assertSame('30100', $dataAres->address->zip);
        self::assertSame('Statutární město Plzeň', $dataAres->companyName);
        self::assertSame('Plzeň', $dataAres->address->city);
        self::assertSame('00075370', $dataAres->companyId);
        self::assertSame('Česká republika', $dataAres->address->country);
        self::assertSame('Vnitřní Město', $dataAres->address->district);
        self::assertSame(
            [
                'city' => 'Plzeň',
                'zip' => '30100',
                'street' => 'náměstí Republiky',
                'streetNo1' => '1',
                'streetNo2' => '1',
                'district' => 'Vnitřní Město',
                'country' => 'Česká republika',
            ], $dataAres->address->toArray(),
        );
    }

    public function testGetIncorrectResult(): void
    {
        $this->expectException(ExternalAresException::class);
        $this->expectErrorMessageMatches('/Problem in ARES: .*/');
        $ares = $this->_getAres();
        $ares->getByCompanyId('11111111');
    }

    public function testStatusError(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(new Response(404));

        $ares = $this->_getAres($client);

        $this->expectException(ExternalAresException::class);
        $this->expectErrorMessage('Problem with connection');
        $ares->getByCompanyId('48136000');
    }

    public function testGuzzleException(): void
    {
        $requst = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $client = $this->createMock(Client::class);
        $client->method('request')->willThrowException(
            new BadResponseException('Test exception', $requst, $response),
        );

        $ares = $this->_getAres($client);

        $this->expectException(ExternalAresException::class);
        $this->expectErrorMessage('Test exception');
        $ares->getByCompanyId('48136000');
    }

    public function testNoData(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('request')->willReturn(new Response(200));

        $ares = $this->_getAres($client);

        $this->expectException(ExternalAresException::class);
        $this->expectErrorMessage('Problem with xml parser');
        $ares->getByCompanyId('48136000');
    }

    public function testReturnedBadData(): void
    {
        $client = $this->createMock(Client::class);
        $xmlData = file_get_contents(__DIR__ . '/data/FakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $client->method('request')->willReturn(
            new Response(200, [], $xmlData),
        );

        $ares = $this->_getAres($client);

        $this->expectException(IncorrectReturnedDataException::class);
        $this->expectErrorMessage('Returned data are bad');

        $ares->getByCompanyId('48136000');
    }

    public function testBadNamespace(): void
    {
        $client = $this->createMock(Client::class);
        $xmlData = file_get_contents(__DIR__ . '/data/InvalidNamespaceFakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $client->method('request')->willReturn(
            new Response(200, [], $xmlData),
        );

        $ares = $this->_getAres($client);

        $this->expectException(ExternalAresException::class);
        $this->expectErrorMessage('Can not load namespace');
        $ares->getByCompanyId('12332112');
    }

    public function testReturnedFakeData(): void
    {
        $client = $this->createMock(Client::class);
        $xmlData = file_get_contents(__DIR__ . '/data/FakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $client->method('request')->willReturn(
            new Response(200, [], $xmlData),
        );

        $ares = $this->_getAres($client);

        $dataAres = $ares->getByCompanyId('12332112');
        self::assertSame('CZ12332112', $dataAres->vatNumber);
        self::assertSame('Test User', $dataAres->companyName);
        self::assertSame('12332112', $dataAres->companyId);
        self::assertSame('26.08.2020 13:23:51', $dataAres->meta->datetime->format('d.m.Y H:i:s'));
        self::assertSame('Josefa Švejka', $dataAres->address->street);
        self::assertSame('1122b', $dataAres->address->streetNo1);
        self::assertSame('2a', $dataAres->address->streetNo2);
        self::assertSame('32325', $dataAres->address->zip);
        self::assertSame('Pardubice', $dataAres->address->city);
        self::assertSame('Česká republika', $dataAres->address->country);
        self::assertSame('Plzeňské Předměstí', $dataAres->address->district);
        self::assertSame(
            [
                'companyName' => 'Test User',
                'vatNumber' => 'CZ12332112',
                'meta' => ['datetime' => '2020-08-26 13:23:51'],
                'address' => [
                    'city' => 'Pardubice',
                    'zip' => '32325',
                    'street' => 'Josefa Švejka',
                    'streetNo1' => '1122b',
                    'streetNo2' => '2a',
                    'district' => 'Plzeňské Předměstí',
                    'country' => 'Česká republika',
                ],
                'fieldOfActivities' => [
                    [
                        'code' => 'Z01007',
                        'title' => 'Výroba potravinářských a škrobárenských výrobků',
                    ],
                    [
                        'code' => 'Z01012',
                        'title' => 'Zpracování dřeva, výroba dřevěných, korkových, proutěných a slaměných výrobků',
                    ],
                    [
                        'code' => 'Z01013',
                        'title' => 'Výroba vlákniny, papíru a lepenky a zboží z těchto materiálů',
                    ],
                ],
            ],
            $dataAres->toArray(),
        );
    }

    public function testNonExistsProperty(): void
    {
        $this->expectException(Throwable::class);
        $client = $this->createMock(Client::class);
        $xmlData = file_get_contents(__DIR__ . '/data/FakeData.xml');

        if (false === $xmlData) {
            throw new InvalidArgumentException('File is not readable');
        }

        $client->method('request')->willReturn(
            new Response(200, [], $xmlData),
        );

        $ares = $this->_getAres($client);

        $dataAres = $ares->getByCompanyId('12332112');
        self::assertNull($dataAres->testProperty);
    }

    /** @param MockObject|Client $stubClient */
    private function _getAres(?MockObject $stubClient = null): Ares
    {
        return new Ares($stubClient ?: new Client());
    }

}

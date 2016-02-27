<?php
/*
* The MIT License (MIT)
*
* Copyright (c) 2015 Pierre Pélisset
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*
*/

namespace OpenLdapObject\Tests\Builder;


use OpenLdapObject\Builder\Condition;
use OpenLdapObject\Builder\Query;
use OpenLdapObject\LdapClient\Connection;
use OpenLdapObject\Manager\EntityManager;
use OpenLdapObject\Manager\Repository;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    private $repository;

    public function setUp()
    {
        $connect = new Connection(LDAP_HOST, LDAP_PORT);
        $connect->identify(LDAP_USER, LDAP_PASSWORD);
        $client = $connect->connect();
        $client->setBaseDn(LDAP_BASE_DN);
        try {
            EntityManager::addEntityManager('default', $client);
        } catch (\InvalidArgumentException $e) {

        }
        $this->repository = EntityManager::getEntityManager()->getRepository('\OpenLdapObject\Tests\Manager\People');
    }

    public function testSimpleQueryBuilder()
    {
        $query = new Query(Query::CAND);
        $query->cOr([
            new Condition('telephoneNumber', '03 00 00 00 01'),
            new Condition('telephoneNumber', '03 00 00 00 00')
        ]);
        $this->assertEquals('(&(|(telephoneNumber=03 00 00 00 01)(telephoneNumber=03 00 00 00 00)))', $query->getQueryForRepository($this->repository));
    }

    public function testHardQueryBuilder()
    {
        $query = new Query(Query::CAND);
        $query->cAnd([
            new Condition('givenName', 'Pierre')
        ]);
        $query->cOr([
            new Condition('telephoneNumber', '03 00 00 00 01'),
            new Condition('telephoneNumber', '03 00 00 00 00')
        ]);
        $this->assertEquals('(&(&(givenName=Pierre))(|(telephoneNumber=03 00 00 00 01)(telephoneNumber=03 00 00 00 00)))', $query->getQueryForRepository($this->repository));
    }

    public function testRepositoryQuery()
    {
        $query = new Query(Query::CAND);
        $query->cOr([
            new Condition('telephoneNumber', '03 00 00 00 01'),
            new Condition('telephoneNumber', '03 00 00 00 00')
        ]);
        $uids = [];
        $result = $this->repository->findByQuery($query);
        foreach ($result as $r) {
            $uids[] = $r->getUid();
        }
        $this->assertEquals(['pdeparis', 'mdupont'], $uids);
    }
}

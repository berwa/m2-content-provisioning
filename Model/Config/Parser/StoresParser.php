<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Config\Parser;

use DOMElement;
use Firegento\ContentProvisioning\Api\ConfigParserInterface;
use Firegento\ContentProvisioning\Api\StoreCodeResolverInterface;
use Firegento\ContentProvisioning\Model\Config\Parser\Query\FetchAttributeValue;

class StoresParser implements ConfigParserInterface
{
    /**
     * @var StoreCodeResolverInterface
     */
    private $storeCodeResolver;

    /**
     * @var FetchAttributeValue
     */
    private $fetchAttributeValue;

    /**
     * @param StoreCodeResolverInterface $storeCodeResolver
     * @param FetchAttributeValue $fetchAttributeValue
     */
    public function __construct(
        StoreCodeResolverInterface $storeCodeResolver,
        FetchAttributeValue $fetchAttributeValue
    ) {
        $this->storeCodeResolver = $storeCodeResolver;
        $this->fetchAttributeValue = $fetchAttributeValue;
    }

    /**
     * @param DOMElement $element
     * @return array
     */
    public function execute(DOMElement $element): array
    {
        $storeCodes = [];
        foreach ($element->getElementsByTagName('store') as $store) {
            $storeCodes[] = $this->storeCodeResolver->execute(
                (string)$this->fetchAttributeValue->execute($store, 'code', '*')
            );
        }

        if (!empty($storeCodes)) {
            $storeCodes = array_merge(...$storeCodes);
        }

        if (empty($storeCodes)) {
            $storeCodes = $this->storeCodeResolver->execute('*');
        }

        return ['stores' => $storeCodes];
    }
}

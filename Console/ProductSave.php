<?php

namespace Codaone\Magerun\Console;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSave extends Command
{
    /** @var \Magento\Framework\App\State */
    protected $state;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Magento\Framework\App\Config\Storage\WriterInterface */
    protected $configWriter;

    /** @var \Magento\Catalog\Api\Data\ProductInterfaceFactory */
    protected $productFactory;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $productRepository;

    /** @var \Magento\Framework\App\Filesystem\DirectoryList */
    protected $directoryList;

    /** @var \Magento\Framework\Filesystem\Io\File */
    protected $file;

    public function __construct(
        \Magento\Framework\App\State\Proxy $state,
        \Magento\Store\Model\StoreManagerInterface\Proxy $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface\Proxy $productRepository,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        $name = null
    ) {
        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->directoryList = $directoryList;
        $this->file = $file;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('codaone:magerun:productsave')
            ->setDescription('Showcase of magerun2 bug');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            // Area is already set, ignore the error
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $product->setSku('test');
        $product->setName('Test Product');
        $product->setStoreId(Store::DEFAULT_STORE_ID);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4); // Default attribute set for products
        $product->setWeight(0);
        $product->setPrice(0);

        $img_url = "https://www.codaone.fi/files/1015/1988/6006/codaone_logo_final.png";
        $img_name = basename($img_url);
        $tmpDir  = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp/';
        $this->file->checkAndCreateFolder($tmpDir);
        $newFileName = $tmpDir . $img_name . '.png';

        // read file from URL and copy it to the new destination
        $result = $this->file->read($img_url, $newFileName);
        if ($result && $this->file->fileExists($newFileName)) {
            $product->addImageToMediaGallery($newFileName, array('image', 'thumbnail', 'small_image'), true, false);
        }

        $this->productRepository->save($product);

        $output->writeln("<info>Saved succesfully product</info>");
    }
}

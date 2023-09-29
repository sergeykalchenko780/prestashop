<?php

namespace PrestaShop\Module\ExtendOrderGrid;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GridModifier
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function modifyGrid(array $params) : void
    {
        $definition = $params['definition'];

        /** @var ColumnCollectionInterface $columns */
        $columns = $definition->getColumns();
        $columns
            ->addAfter(
                'payment',
                (new DataColumn('delay'))
                ->setName($this->translator->trans('Carrier'))
                ->setOptions([
                    'field' => 'delay',
                    'sortable' => true,
                ])
            );

        /** @var FilterCollectionInterface $filters */
        $filters = $definition->getFilters();
        $filters->add(
            (new Filter('delay', TextType::class))
            ->setAssociatedColumn('delay')
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('Search Carrier', [], 'Admin.Actions'),
                ],
            ])
        );
    }
}

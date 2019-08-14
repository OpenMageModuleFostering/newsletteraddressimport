<?php

class D3_Newsladdressimport_Block_Adminhtml_Newsladdressimport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('newsladdressimportGrid');
      $this->setDefaultSort('newsladdressimport_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('newsladdressimport/newsladdressimport')->getCollection();
      foreach ($collection as $view) {
        if ( $view->getStoreId() && $view->getStoreId() != 0 ) {
          $view->setStoreId(explode(',',$view->getStoreId()));
        } else {
          $view->setStoreId(array('0'));
        }
      }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('newsladdressimport_id', array(
          'header'    => Mage::helper('newsladdressimport')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'newsladdressimport_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('newsladdressimport')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('newsladdressimport')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

    if ( !Mage::app()->isSingleStoreMode() ) {
      $this->addColumn('store_id', array(
        'header' => Mage::helper('newsladdressimport')->__('Store View'),
        'index' => 'store_id',
        'type' => 'store',
        'store_all' => true,
        'store_view' => true,
        'sortable' => true,
        'filter_condition_callback' => array($this, '_filterStoreCondition'),
      ));
    }

      $this->addColumn('status', array(
          'header'    => Mage::helper('newsladdressimport')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('newsladdressimport')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('newsladdressimport')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('newsladdressimport')->__('Import'),
                        'url'       => array('base'=> '*/*/import'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

		$this->addExportType('*/*/exportCsv', Mage::helper('newsladdressimport')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('newsladdressimport')->__('XML'));

      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('newsladdressimport_id');
        $this->getMassactionBlock()->setFormFieldName('newsladdressimport');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('newsladdressimport')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('newsladdressimport')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('import', array(
             'label'    => Mage::helper('newsladdressimport')->__('Import addresses'),
             'url'      => $this->getUrl('*/*/massImport'),
             'confirm'  => Mage::helper('newsladdressimport')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('newsladdressimport/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('newsladdressimport')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('newsladdressimport')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  protected function _filterStoreCondition($collection, $column)
  {
    if ( !$value = $column->getFilter()->getValue() ) {
      return;
    }
    // $this->getCollection()->addStoreFilter($value);
  }

}

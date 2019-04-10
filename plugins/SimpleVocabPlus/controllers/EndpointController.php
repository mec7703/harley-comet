<?php
/**
 * Simple Vocab Plus
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Simple Vocab Plus Endpoint controller.
 * 
 * @package SvPlus
 */
class SimpleVocabPlus_EndpointController extends Omeka_Controller_AbstractActionController
{
    
    /**
     * Outputs the suggest endpoint URL of the specified element or NULL if 
     * there is none.
     */
    public function vocabAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $elementId = $this->getRequest()->getParam('element_id');
        $svAssign = $this->_helper->db->getTable('SvpAssign')->findByElementId($elementId);
        $vocab = $this->_helper->db->getTable('SvpAssign')->find($svAssign->vocab_id);
        echo $vocab ? $vocab->url : null;
    }

    /**
     * Proxy for the Sv Suggest suggest endpoints, used by the 
     * autosuggest feature.
     */
    public function suggestProxyAction()
    {
      //get the term
      $term = $this->getRequest()->getParam('term');

        // Get the suggest record.
      $elementId = $this->getRequest()->getParam('element-id');
      $svAssigns = $this->_helper->db->getTable('SvpAssign')->findByElementId($elementId);
      $return = array();
      $termTable = $this->_helper->db->getTable('SvpTerm');
      foreach($svAssigns as $svAssign) {
          $results = $termTable->findBySql('vocab_id = ? and term like ?',array($svAssign->vocab_id,$term.'%'));
          foreach($results as $result) {
              $return[] = $result->term;
          }
      }
      $this->_helper->json($return);
    }



}
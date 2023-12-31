<?php
namespace Opencart\Catalog\Model\Extension\Tmdcustomheadermenu\Tmd;
class Tmdheader extends \Opencart\System\Engine\Controller {
	
	////////////////////////////////////////Header//////////////////////////////////////////
	public function getHeadermenu(){
	 
			$data=[];
			$query =$this->db->query("SELECT * FROM " . DB_PREFIX . "headermenu h LEFT JOIN " . DB_PREFIX . "headermenu_description hd ON (h.headermenu_id = hd.headermenu_id) where hd.language_id = '" . (int)$this->config->get('config_language_id') . "' and h.level1=0 and h.level2=0 and h.status=1 order by h.sort_order");

			
			foreach($query->rows as $row){
				$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "headermenu h LEFT JOIN " . DB_PREFIX . "headermenu_description hd ON (h.headermenu_id = hd.headermenu_id) where hd.language_id = '" . (int)$this->config->get('config_language_id') . "' and h.level1='".$row['headermenu_id']."' and  h.status=1 order by h.sort_order" );
				
				 $subtitle=[];
				foreach($query1->rows as $row1){
				
					$subtitlenew=[];
				
				$query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "headermenu h LEFT JOIN " . DB_PREFIX . "headermenu_description hd ON (h.headermenu_id = hd.headermenu_id) where hd.language_id = '" . (int)$this->config->get('config_language_id') . "' and h.status=1 and h.level2='".$row1['headermenu_id']."' order by h.sort_order");
					foreach($query2->rows as $row2){
					$subtitlenew[]=array('title' => $row2['title'],'column' => $row2['column'],'link' =>$row2['link'],'sub_title' => $subtitlenew);
				}
				
					$subtitle[]=array('title' => $row1['title'],'column' => $row1['column'],'link' =>$row1['link'],'sub_title' => $subtitlenew);
				} 
				
				$data[]=[
					'title' => $row['title'],
					'link' => $row['link'],
					'column' => $row['column'],
					'sub_title' => $subtitle 
				];
			}
			return $data;
		}	
	}
?>
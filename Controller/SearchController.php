<?php
App::uses('AppController', 'Controller');
class SearchController extends AppController {
	public $name = 'Search';
	public $uses = array('Pegawai','Keluarga','Mutasi');
	public $components = array('Paginator', 'Flash', 'Session');
	

	
	public function index() {
		if ($this->request->is('post')) {
			
			/* SETTING PENCARIAN ID EXPATS, KITAS, IMTAS, RPTKA */
			
			$idExpatsSearch = array(); 
			if(!empty($this->data['Expat'])){  
				$idexpat = array();
				foreach($this->data['Expat']['id'] as $key => $vals){
					$idExpatsSearch[] = $key;
					$idexpat[] = $key;
				}
				$idexpat = implode(',', $idexpat); 
			}		 

			if(!empty($this->data['Kita'])){ 
				$idimtaX = array();
				foreach($this->data['Kita']['id'] as $key => $vals){
					$idimtaX[] = $this->Kita->findAllIdKita($vals);
					$x = $this->Kita->findAllIdExpatsByCompanyName($vals); 
					for($i=0;$i<count($x);$i++){ 
						if(!empty($x[$i])){
							$idExpatsSearch[] = $x[$i];
						}
					}
				}  
				$idkita = array();
				foreach($idimtaX as $key => $vals){ 
					for($i=0;$i<count($vals);$i++){ 
						if(!empty($vals[$i])){
							$idkita[] = $vals[$i];
						}
					}
				}  
				$idkita = implode(',', $idkita); 
			}
			
			if(!empty($this->data['Imta'])){  
				$idimtaX = array();
				foreach($this->data['Imta']['id'] as $key => $vals){ 
					$idimtaX[] = $this->Imta->findAllIdImta($vals);
					$x = $this->Imta->findAllIdExpatsByCompanyName($vals); 
					for($i=0;$i<count($x);$i++){ 
						if(!empty($x[$i])){
							$idExpatsSearch[] = $x[$i];
						}
					}
				} 
				$idimta = array();
				foreach($idimtaX as $key => $vals){ 
					for($i=0;$i<count($vals);$i++){ 
						if(!empty($vals[$i])){
							$idimta[] = $vals[$i];
						}
					}
				}  
				$idimta = implode(',', $idimta);  
			}
			
			if(!empty($this->data['Rptka'])){ 
				$idrptkaX = array();
				foreach($this->data['Rptka']['id'] as $key => $vals){
					$idrptkaX[] = $this->Rptka->findAllIdRptka($vals);
					$x = $this->Rptka->findAllIdExpatsByCompanyName($vals); 
					for($i=0;$i<count($x);$i++){ 
						if(!empty($x[$i])){
							$idExpatsSearch[] = $x[$i];
						}
					}
				}  
				$idrptka = array();
				foreach($idrptkaX as $key => $vals){ 
					for($i=0;$i<count($vals);$i++){ 
						if(!empty($vals[$i])){
							$idrptka[] = $vals[$i];
						}
					}
				}  
				$idrptka = implode(',', $idrptka);   
			}
			
			if(!empty($this->data['Merp'])){ 
				$idmerpX = array();
				foreach($this->data['Merp']['id'] as $key => $vals){
					$idmerpX[] = $this->Merp->findAllIdMerp($vals);
					$x = $this->Merp->findAllIdExpatsByCompanyName($vals); 
					for($i=0;$i<count($x);$i++){ 
						if(!empty($x[$i])){
							$idExpatsSearch[] = $x[$i];
						}
					}
				}  
				$idmerp = array();
				foreach($idmerpX as $key => $vals){ 
					for($i=0;$i<count($vals);$i++){ 
						if(!empty($vals[$i])){
							$idmerp[] = $vals[$i];
						}
					}
				}  
				$idmerp = implode(',', $idmerp);   
			}	

			if(!empty($this->data['Dpkk'])){ 
				$iddpkkX = array();
				foreach($this->data['Dpkk']['id'] as $key => $vals){
					$iddpkkX[] = $this->Dpkk->findAllIdDpkk($vals);
					$x = $this->Dpkk->findAllIdExpatsByCompanyName($vals); 
					for($i=0;$i<count($x);$i++){ 
						if(!empty($x[$i])){
							$idExpatsSearch[] = $x[$i];
						}
					}
				}  
				$iddpkk = array();
				foreach($iddpkkX as $key => $vals){ 
					for($i=0;$i<count($vals);$i++){ 
						if(!empty($vals[$i])){
							$iddpkk[] = $vals[$i];
						}
					}
				}  
				$iddpkk = implode(',', $iddpkk);   
			}			
			 
			$inId = array_unique($idExpatsSearch); 
			$expatID = implode(', ', $inId); 

			/* END SETTING PENCARIAN ID EXPATS, KITAS, IMTAS, RPTKA, MERP, DPKK */
			
			/* REDIRECT HALAMAN */
			$this->redirect(array('action' => 'index', '?expatid='.$expatID.'&idexpat='.@$idexpat.'&idkita='.@$idkita.'&idimta='.@$idimta.'&idrptka='.@$idrptka.'&idmerp='.@$idmerp.'&iddpkk='.@$iddpkk.'&start='.$this->data['Tahun']['start']['year'].'&end='.$this->data['Tahun']['end']['year']));
					
		}	
		
		if(!empty($this->params->query['expatid'])){  
			
			/* CONFIGURASI CONTAIN */
			$kitaContain[] = $this->params->query['idkita'] != '' ? "Kita.id IN ({$this->params->query['idkita']})" : '';
			$imtaContain[] = $this->params->query['idimta'] != '' ? "Imta.id IN ({$this->params->query['idimta']})" : '';
			$rptkaContain[] = $this->params->query['idrptka'] != '' ? "Rptka.id IN ({$this->params->query['idrptka']})" : '';
			$merpContain[] = $this->params->query['idmerp'] != '' ? "Merp.id IN ({$this->params->query['idmerp']})" : '';
			$dpkkContain[] = $this->params->query['iddpkk'] != '' ? "Dpkk.id IN ({$this->params->query['iddpkk']})" : '';
			
			/* FILTERING TANGGAL */
			
			if(!emptY($this->params->query['start']) && !empty($this->params->query['end'])){
				$kitaContain[] = array(
					'YEAR(DATE(Kita.startdate)) >= ' => $this->params->query['start'],
					'YEAR(DATE(Kita.enddate)) <= ' => $this->params->query['end'],
				);
				$imtaContain[] = array(
					'YEAR(DATE(Imta.startdate)) >= ' => $this->params->query['start'],
					'YEAR(DATE(Imta.enddate)) <= ' => $this->params->query['end'],
				);
				$rptkaContain[] = array(
					'YEAR(DATE(Rptka.startdate)) >= ' => $this->params->query['start'],
					'YEAR(DATE(Rptka.enddate)) <= ' => $this->params->query['end'],
				);
				$merpContain[] = array(
					'YEAR(DATE(Merp.startdate)) >= ' => $this->params->query['start'],
					'YEAR(DATE(Merp.enddate)) <= ' => $this->params->query['end'],
				);			
				$dpkkContain[] = array(
					'YEAR(DATE(Dpkk.tglbayar)) LIKE ' => $this->params->query['start'].'%',
					//'YEAR(DATE(Dpkk.enddate)) <= ' => $this->params->query['end'],
				);	
			}
			/* END FILTERING TANGGAL */
			
			
			$contains = array( 
				'Kita' => array(
					'conditions' => array($kitaContain, @$yearKita[0])
				),
				'Imta' => array(
					'conditions' => array($imtaContain,)
				),
				'Rptka' => array(
					'conditions' => array($rptkaContain,)
				),
				'Merp' => array(
					'conditions' => array($merpContain,)
				),
				'Dpkk' => array(
					'conditions' => array($dpkkContain,)
				)
			);  
			
			/* ENDCONFIGURASI CONTAIN */
			
			/* PAGINATE */
			$this->Expat->recursive = 2;
			$conditions[] = array("Expat.id IN ({$this->params->query['expatid']})"); 
			$this->paginate = array(
				'contain' => $contains,
				'fields' => array(),
				'conditions' => $conditions,
				'order' => 'Expat.id ASC',
				'limit' => 20
			); 
			$expats = $this->paginate('Expat');
			/* ENDPAGINATE */	
			
			
			if(empty($expats)){ 
				$this->Flash->error(__('Hasil Pencarian Tidak ditemukan'));
			} 
			
			/* SET OUTPUT */
			$this->set('expats',$expats); 
		}
		
		
		
		/* DEFAULT OUTPUT */
		
		$this->Expat->recursive = 0;
		$this->set('Expat',$this->Expat->find('all',array('fields' => array('Expat.id','Expat.name')))); 		
		$this->set('Kita',$this->Kita->find('all',array('fields' => array('Kita.id','Kita.expat_id','Kita.companyname'), 'group' => 'Kita.companyname', 'order' => 'Kita.id ASC')));
		$this->set('Imta',$this->Imta->find('all',array('fields' => array('Imta.id','Imta.expat_id','Imta.companyname'), 'group' => 'Imta.companyname', 'order' => 'Imta.id ASC')));
		$this->set('Rptka',$this->Rptka->find('all',array('fields' => array('Rptka.id','Rptka.expat_id','Rptka.companyname'), 'group' => 'Rptka.companyname', 'order' => 'Rptka.id ASC')));
		$this->set('Merp',$this->Merp->find('all',array('fields' => array('Merp.id','Merp.expat_id','Merp.companyname'), 'group' => 'Merp.companyname', 'order' => 'Merp.id ASC')));
		$this->set('Dpkk',$this->Dpkk->find('all',array('fields' => array('Dpkk.id','Dpkk.expat_id','Dpkk.companyname'), 'group' => 'Dpkk.companyname', 'order' => 'Dpkk.id ASC')));
		
		/* ENDDEFAULT OUTPUT */
		
	}
	
	//tambahan untuk search by location + grade + jabatan
	
	

/********************************************** bismillah ******************************/

	public function index3() {		
		if ($this->request->is('post')) {
		
			$pilihan  = array();  $sex   = array();
			$pilihan2 = array();  $sex2  = array();
			$pilihan3 = array();  $position2 = array();
			$grade    = array();  $ethnic= array();
			$grade2   = array();
			$grade3   = array();
			
			//---------- jika hanya ethnic  yang diisi
			if(isset($_POST['ethnic'])) {
				foreach ($_POST['ethnic'] as $key => $selectedOption) :
					$ethnic[] = $selectedOption;			
				endforeach;	
				$ethnic = implode("','", $ethnic);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "Pegawai.suku IN ('$ethnic')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.suku ASC',
					'limit' => 999,
					'maxLimit' => 999
				); 
			
			}
			
			//---------- jika hanya location  yang diisi
			if(isset($_POST['pilihan'])) {
				foreach ($_POST['pilihan'] as $key => $selectedOption) :
					$pilihan[] = $selectedOption;			
				endforeach;	
				$pilihan = implode("','", $pilihan);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "Pegawai.location IN ('$pilihan')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999
				); 
			
			}
			//---------- jika hanya  grade yang diisi
			if(isset($_POST['grade'])) {
				foreach ($_POST['grade'] as $key => $selectedOption) :
					$grade[] = $selectedOption;			
				endforeach;	
				$grade = implode("','", $grade);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "Pegawai.grade IN ('$grade')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			//---------- jika status + sex + position yang diisi
			if(isset($_POST['status']) && isset($_POST['sex'])   && isset($_POST['position'])) {	
								
				foreach ($_POST['status'] as $key => $selectedOption) :
					$status[] = $selectedOption;			
				endforeach;	
				$status = implode("','", $status);
				
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position);		
						
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.status IN ('$status')","Pegawai.jeniskelamin IN ('$sex')","Pegawai.jabatan IN ('$position')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					//'limit' => 999,
					//'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika sex + position yang diisi
			if(isset($_POST['sex'])  && isset($_POST['position'])) {	
								
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex2[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex2);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position2[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position2);		
						
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.jeniskelamin IN ('$sex')","Pegawai.jabatan IN ('$position')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 600,
					'maxLimit' => 700
				); 
			
			}
			
			//---------- jika sex + position + dob yang diisi
			elseif(isset($_POST['sex'])  && isset($_POST['position']) && !empty($_POST['dobstart']) && !empty($_POST['dobend'])) {	
								
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex2[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex2);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position2[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position2);
				
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
				
						
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.jeniskelamin IN ('$sex')","Pegawai.jabatan IN ('$position')","AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					//'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika sex + position + dob + doj yang diisi
			elseif(isset($_POST['sex'])  && isset($_POST['position']) && !empty($_POST['dobstart']) && !empty($_POST['dobend']) && !empty($_POST['dojstart']) && !empty($_POST['dojend'])) {	
								
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex2[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex2);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position2[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position2);
				
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
				
				$startj = explode("/",$_POST['dojstart']); 
				$endj   = explode("/",$_POST['dojend']);
						
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.jeniskelamin IN ('$sex')","Pegawai.jabatan IN ('$position')","AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'","Pegawai.doj >= '$startj[2]-$startj[0]-$startj[1]'", "Pegawai.doj <= '$endj[2]-$endj[0]-$endj[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika location + sex  yang diisi
			if(isset($_POST['sex'])  && isset($_POST['pilihan'])) {	
								
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex2[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex2);				
				 
				
				foreach ($_POST['pilihan'] as $key => $selectedOption2) :
					$pilihan2[] = $selectedOption2;			
				endforeach;	
				$pilihan2 = implode("','", $pilihan2);
				
										
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.jeniskelamin IN ('$sex')","Pegawai.location IN ('$pilihan2')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika location + sex + position + dob + doj yang diisi
			if(isset($_POST['sex'])  && isset($_POST['pilihan']) && isset($_POST['position']) && !empty($_POST['dobstart']) && !empty($_POST['dobend']) && !empty($_POST['dojstart']) && !empty($_POST['dojend'])) {	
								
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex2[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex2);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position2[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position2);
				
				foreach ($_POST['pilihan'] as $key => $selectedOption2) :
					$pilihan2[] = $selectedOption2;			
				endforeach;	
				$pilihan2 = implode("','", $pilihan2);
				
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
				
				$startj = explode("/",$_POST['dojstart']); 
				$endj   = explode("/",$_POST['dojend']);
						
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.jeniskelamin IN ('$sex')","Pegawai.jabatan IN ('$position')","Pegawai.location IN ('$pilihan2')","AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'","Pegawai.doj >= '$startj[2]-$startj[0]-$startj[1]'", "Pegawai.doj <= '$endj[2]-$endj[0]-$endj[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika hanya location dan grade yang diisi
			if(isset($_POST['grade']) && isset($_POST['pilihan'])) {
				
				foreach ($_POST['grade'] as $key => $selectedOption1) :
					$grade2[] = $selectedOption1;			
				endforeach;	
				
				foreach ($_POST['pilihan'] as $key => $selectedOption2) :
					$pilihan2[] = $selectedOption2;			
				endforeach;	
				
				
				$pilihan2 = implode("','", $pilihan2);
				$grade2 = implode("','", $grade2);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "Pegawai.grade IN ('$grade2')", "Pegawai.location IN ('$pilihan2')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999
				); 
			
			}
			
			//---------- jika hanya tanggal lahir yang diisi
			if(!empty($_POST['dobstart']) && !empty($_POST['dobend'])) {
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika hanya location dan tanggal lahir yang diisi
			if(!empty($_POST['dobstart']) && !empty($_POST['dobend']) && isset($_POST['pilihan'])) {
				foreach ($_POST['pilihan'] as $key => $selectedOption3) :
					$pilihan3[] = $selectedOption3;			
				endforeach;	
				$pilihan3 = implode("','", $pilihan3);
			
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.location IN ('$pilihan3')", "AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			/*/---------- jika hanya location dan tanggal join yang diisi
			if(!empty($_POST['dojstart']) && !empty($_POST['dojend']) && isset($_POST['pilihan'])) {
				foreach ($_POST['pilihan'] as $key => $selectedOption3) :
					$pilihan3[] = $selectedOption3;			
				endforeach;	
				$pilihan3 = implode("','", $pilihan3);
			
				$start = explode("/",$_POST['dojstart']); 
				$end   = explode("/",$_POST['dojend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.location IN ('$pilihan3')", "AND"=>array("Pegawai.doj >= '$start[2]-$start[0]-$start[1]'", "Pegawai.doj <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			*/
			
			//---------- jika hanya tanggal join yang diisi
			if(!empty($_POST['dojstart']) && !empty($_POST['dojend'])) {
					
				$start = explode("/",$_POST['dojstart']); 
				$end   = explode("/",$_POST['dojend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "AND"=>array("Pegawai.doj >= '$start[2]-$start[0]-$start[1]'", "Pegawai.doj <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika hanya tanggal lahir dan tanggal join yang diisi
			if(!empty($_POST['dojstart']) && !empty($_POST['dojend']) && !empty($_POST['dobstart']) && !empty($_POST['dobend'])) {
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
				
				$start2 = explode("/",$_POST['dojstart']); 
				$end2   = explode("/",$_POST['dojend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF', "AND"=>array("Pegawai.doj >= '$start2[2]-$start2[0]-$start2[1]'", "Pegawai.doj <= '$end2[2]-$end2[0]-$end2[1]'","Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'","Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			//---------- jika location + grade + tanggal lahir yang diisi
			if(!empty($_POST['dobstart']) && !empty($_POST['dobend']) && isset($_POST['grade']) && isset($_POST['pilihan'])) {
				foreach ($_POST['pilihan'] as $key => $selectedOption4) :
					$pilihan4[] = $selectedOption4;			
				endforeach;	
				$pilihan3 = implode("','", $pilihan4);
				
				foreach ($_POST['grade'] as $key => $selectedOption3) :
					$grade3[] = $selectedOption3;			
				endforeach;	
				$grade3 = implode("','", $grade3);
			
				$start = explode("/",$_POST['dobstart']); 
				$end   = explode("/",$_POST['dobend']);
			
				$conditions[] = array('Pegawai.status2' => 'AKTIF',"Pegawai.location IN ('$pilihan3')", "Pegawai.grade IN ('$grade3')", "AND"=>array("Pegawai.lahir >= '$start[2]-$start[0]-$start[1]'", "Pegawai.lahir <= '$end[2]-$end[0]-$end[1]'")); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			
			//---------- jika location + status yang diisi
			if(isset($_POST['pilihan']) && isset($_POST['status'])) {
			
				foreach ($_POST['pilihan'] as $key => $selectedOption) :
					$pilihan1[] = $selectedOption;			
				endforeach;	
				$pilihan = implode("','", $pilihan1);
				
				foreach ($_POST['status'] as $key => $selectedOption) :
					$status[] = $selectedOption;			
				endforeach;	
				$status = implode("','", $status);
						
				$conditions[] = array("Pegawai.status IN ('$status')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			/*/---------- jika location + sex yang diisi
			if(isset($_POST['pilihan']) && isset($_POST['sex'])) {
			
				foreach ($_POST['pilihan'] as $key => $selectedOption) :
					$pilihan1[] = $selectedOption;			
				endforeach;	
				$pilihan = implode("','", $pilihan1);
				
				foreach ($_POST['sex'] as $key => $selectedOption) :
					$sex[] = $selectedOption;			
				endforeach;	
				$sex = implode("','", $sex);
						
				$conditions[] = array("Pegawai.jeniskelamin IN ('$sex')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}*/
			
			//---------- jika location + agama/religion yang diisi
			if(isset($_POST['pilihan']) && isset($_POST['agama'])) {
			
				foreach ($_POST['pilihan'] as $key => $selectedOption) :
					$pilihan1[] = $selectedOption;			
				endforeach;	
				$pilihan = implode("','", $pilihan1);
				
				foreach ($_POST['agama'] as $key => $selectedOption) :
					$agama[] = $selectedOption;			
				endforeach;	
				$agama = implode("','", $agama);
						
				$conditions[] = array("Pegawai.agama IN ('$agama')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}
			
			/*/---------- jika location + position yang diisi
			if(isset($_POST['pilihan']) && isset($_POST['position'])) {
			
				foreach ($_POST['pilihan'] as $key => $selectedOption) :
					$pilihan1[] = $selectedOption;			
				endforeach;	
				$pilihan = implode("','", $pilihan1);
				
				foreach ($_POST['position'] as $key => $selectedOption) :
					$position[] = $selectedOption;			
				endforeach;	
				$position = implode("','", $position);
						
				$conditions[] = array("Pegawai.jabatan IN ('$position')"); 
				$this->paginate = array(				
					'fields' => array(),
					'conditions' => $conditions,
					'order' => 'Pegawai.location ASC',
					'limit' => 999,
					'maxLimit' => 999					
				); 
			
			}*/
			
			
			$this->Pegawai->recursive = 0;			
			
			
			/* ENDPAGINATE */	
			$pilihan = $this->paginate('Pegawai');
			
			/* SET OUTPUT */
			$this->set('pilihanku',$pilihan); 
			
		} 
		
		$this->Pegawai->recursive = 0;
		$this->set('Pegawai',$this->Pegawai->find('all',array('fields' => array('Pegawai.id','Pegawai.name','Pegawai.location'),'group' =>
		'Pegawai.location','order'=> 'Pegawai.location ASC'))); 		
		$this->set('Grade',$this->Pegawai->find('all',array('fields' => array('Pegawai.id','Pegawai.name','Pegawai.grade'),'group' => 'Pegawai.grade','order'=> 'Pegawai.grade ASC'))); 
		$this->set('Jabatan',$this->Pegawai->find('all',array('fields' => array('Pegawai.id','Pegawai.name','Pegawai.jabatan'),'group' => 'Pegawai.jabatan','order'=> 'Pegawai.jabatan ASC')));		
		$this->set('Suku',$this->Pegawai->find('all',array('fields' => array('Pegawai.id','Pegawai.name','Pegawai.suku'),'group' => 'Pegawai.suku','order'=> 'Pegawai.suku ASC')));
		
		
	}	
	
}
?>
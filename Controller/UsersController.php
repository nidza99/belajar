	public function add() {
		if ($this->request->is('post')) { 
			$this->User->create();
		// tambahan untuk save image
		//Check if image has been uploaded
                if(!empty($this->request->data['User']['upload']['name']))
                {
                        $file = $this->request->data['User']['upload']; //put the data into a var for easy use

                        $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                        $arr_ext = array('jpg', 'jpeg', 'gif'); //set allowed extensions

                        //only process if the extension is valid
                        if(in_array($ext, $arr_ext))
                        {
                                //do the actual uploading of the file. First arg is the tmp name, second arg is 
                                //where we are putting it.
								//WWW_ROOT = c:\wamp\www\hrad2\webroot
                                move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/uploads/users/' . $file['name']);

                                //prepare the filename for database entry
                                $this->request->data['User']['image'] = $file['name'];
                        }
                }// end of tambahan			
			$this->request->data['Log'][0]['log'] = "penambahan user baru ".$this->request->data['User']['username'];
				
			if ($this->User->saveAssociated($this->request->data, array('deep'=>true))) {
				
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
			
		}
		
	}

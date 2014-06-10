<div class="users form">
<?php echo $this->Form->create('User', array(//'url' => array('action' => 'create'), 
'enctype' => 'multipart/form-data')); ?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('password');		
		echo $this->Form->input('role', array('options' => array('admin' => 'Admin', 'author' => 'Author')));
		echo $this->Form->input('upload', array('label'=>'Your Picture','type' => 'file')); 
		echo $this->Form->hidden('Log.log', array('value' => 'penambahan user'));			
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>

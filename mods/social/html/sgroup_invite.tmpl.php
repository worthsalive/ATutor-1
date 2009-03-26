<?php //debug($this->group_obj); ?>
<div>
	<div style="float:left;">
		<div>
			<span><?php echo _AT('added_members'); ?></span>
			<ul>
			<?php foreach ($this->group_obj->getGroupMembers() as $k=>$person_obj): ?>
				<li><?php echo printSocialName($person_obj->getID()); ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>


	<div style="float:left;width:20%;">
		<form class="input-form" method="POST" action="mods/social/groups/invite.php">
			<span><?php echo _AT('not_added_members'); ?></span>
			<label for="search_not_added_members"><?php echo _AT('search');?></label>
			<input type="text" name="search_not_added_members" id="search_not_added_members">
			<div style="height:300px;overflow:scroll;">
				<?php 
				$all_friends = getFriends($_SESSION['member_id']);
				foreach ($all_friends as $k=>$member_id): 
					if(in_array(new Member($member_id), $this->group_obj->getGroupMembers())){
						$extra = ' disabled="disabled"';
					} else {
						$extra = '';
					}

					if(isset($_POST['new_members'][$member_id])){
						$extra .= ' checked="checked"';
					}
				?>
					<input type="checkbox" name="new_members[<?php echo $member_id;?>]" id="member_<?php echo $member_id; ?>" <?php echo $extra;?>/>
					<label for="member_<?php echo $member_id; ?>"><?php echo printSocialName($member_id, false); ?></label><br/>
				<?php endforeach; ?>
			</div>
			<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
			<input class="button" type="submit" name="inviteMember" value="<?php echo _AT('invite');?>" />
		</form>
	</div>
</div>
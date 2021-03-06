<?php

/**
 * This is the model class for table "profile".
 *
 * The followings are the available columns in table 'profile':
 * @property integer $id
 * @property string $ug_id
 * @property string $hobbies
 * @property string $avatar
 * @property string $signature_image
 */
class Profile extends CActiveRecord
{

	public $avatar_folder='/upload/main/bc1';
	public $signature_folder='/upload/signs';
	public $image;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Profile the static model class
	 */
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{usergroups_user_profile}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ug_id', 'length', 'max'=>20),
			array('birthday, request_signature, signature_image', 'length', 'max'=>120),
			array('site, avatar, request_from, request_address, ', 'length', 'max'=>255),
			array('aboutme', 'length'),
			array('reply_to_email_only', 'numerical', 'allowEmpty'=>true),
			array('site', 'url','allowEmpty'=>true),
			array('image', 'file', 'types'=>'jpeg, jpg, gif, png', 'allowEmpty' => true),
			array('userpic', 'file', 'types'=>'jpeg, jpg, gif, png', 'allowEmpty' => true),
			array('signature_image', 'file', 'types'=>'jpeg, jpg, gif, png', 'allowEmpty' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ug_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	
	public function getDateValue($attr)
	{
	  if (!$this->$attr || $this->$attr=='0000-00-00') return '';
  	  else return date('d.m.Y',CDateTimeParser::parse($this->$attr, 'yyyy-MM-dd'));
	}	
	
	public function beforeValidate(){
		parent::beforeValidate();
		if ($this->birthday && $this->birthday!='0000-00-00') $this->birthday=date('Y-m-d',CDateTimeParser::parse($this->birthday, 'dd.MM.yyyy'));		
		return true;
	}
	
	public function beforeSave(){ 
		parent::beforeSave();

		// saving userpic (avatar):		
		$picture=CUploadedFile::getInstance($this,'image');
		if ($picture){				
	            $imagename=$picture->getTempName();
		    $image = Yii::app()->image->load($imagename);
		    if ($image){
			if ($this->avatar) {
				unlink ($_SERVER['DOCUMENT_ROOT'].$this->avatar_folder.'/'.$this->avatar);
			}
			if ($image->width >= $image->height) {
				$image->resize(20000,93)->rotate(0)->quality(90)->sharpen(20);
			} else 	{
				$image->resize(93, 20000)->rotate(0)->quality(90)->sharpen(20);
			}
			$image->crop(93, 93);
			$file_name=rand().'.'.$picture->extensionName;
			$savename=$_SERVER['DOCUMENT_ROOT'].$this->avatar_folder.'/'.$file_name;
			$image->save($savename);
			$this->avatar=$file_name;
		    }
            	}
		
		// saving handwritten signature:
		$picture=CUploadedFile::getInstance($this,'signature_image');
		if ($picture){				
	            $imagename=$picture->getTempName();
		    $image = Yii::app()->image->load($imagename);
		    if ($image){
			if ($this->signature_image) {
				unlink ($_SERVER['DOCUMENT_ROOT'].$this->signature_folder.'/'.$this->signature_image);
			}

			$image->resize(480, 140)->rotate(0)->quality(90)->sharpen(20);
			//$image->crop(600, 600);
			$file_name=rand().'.'.$picture->extensionName;
			$savename=$_SERVER['DOCUMENT_ROOT'].$this->signature_folder.'/'.$file_name;
			$image->save($savename);
			$this->signature_image=$file_name;
		    }
            	}
		
		return true;
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ug_id' => 'Ug',
			'birthday' => Yii::t('profile', 'BIRTHDAY'),
			'site' => Yii::t('profile', 'HOMEPAGE'),
			'avatar' => Yii::t('profile', 'AVATAR'),
			'image' => Yii::t('profile', 'UPLOAD_PICTURE'),
			'aboutme'=>Yii::t('profile', 'ABOUT'),
			'request_from'=>Yii::t('profile', 'REQUEST_FROM'),
			'request_signature'=>Yii::t('profile', 'SHOWFULLNAME'),
			'request_address'=>Yii::t('profile', 'SHOWABOUTME'),
			'signature_image'=>Yii::t('profile', 'UPLOAD_SIGNATURE_IMAGE'),
			'reply_to_email_only'=>Yii::t('profile', 'REQUEST_REPLY_TO_EMAIL_ONLY'),
		);
	}

	/**
	 * returns an array that contains the views name to be loaded
	 * @return array
	 */
	public function profileViews()
	{
		return array(
			UserGroupsUser::VIEW => 'index',
			UserGroupsUser::EDIT => 'update',
			UserGroupsUser::REGISTRATION => 'register'
		);
	}


	/**
	 * returns an array that contains the name of the attributes that will
	 * be stored in session
	 * @return array
	 */
	public function profileSessionData()
	{
		return array(
			'avatar',
			'signature_image',
			'reply_to_email_only',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('ug_id',$this->ug_id,true);
//		$criteria->compare('hobbies',$this->hobbies,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}

<?php
/**
 * ������ �������������:
 * $captcha = new Module_Captcha_Model_Captcha('./path/to/font.ttf');
 * $_SESSION['code'] = $captcha->getCode();
 * $captcha->make();
 * $captcha->getGdResource(); // �������� ������ ���������� GD
 */
class Module_Captcha_Model_Captcha extends Module_Common_Model_ImagePng
{
    /**
     * �������� ��� �����.
     *
     * @access private
     * @var string
     */
    private $code;

    public function __construct($ttf)
    {
        parent::__construct($ttf);

        $this->code = substr(preg_replace('/[a-z]/', '', md5(time().rand(1,100))), 0, 4);
    }

    /**
     * ���������� �������� ��� �����.
     *
     * @access public
     * @param void
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * ������� ����������� �����.
     *
     * @access public
     * @param void
     * @return void
     */
    public function create()
    {
        $iw = 121;
		$ih = 51;

		$this->iresource = imagecreatetruecolor($iw, $ih);

		$w = imagecolorallocate($this->iresource, 255, 255, 255);

		imagefill($this->iresource, 0, 0, $w);

		// ���� ������
		$g1 = imagecolorallocate($this->iresource, 192, 192, 192);

		// ������ ������������ �����
		for ($i=0; $i<=$iw; $i+=5) imageline($this->iresource,$i,0,$i,$ih,$g1);

		// ������ �������������� �����
		for ($i=0; $i<=$ih; $i+=5) imageline($this->iresource,0,$i,$iw,$i,$g1);

		imagettftext($this->iresource, rand(25,35), rand(-7,7), 10+rand(-5,5), $ih-10+rand(-5,5),
		$this->get_rand_color($this->iresource), $this->path_ttf, substr($this->code,0,1));

		imagettftext($this->iresource, rand(25,35), rand(-7,7), 30+rand(-5,5), $ih-10+rand(-5,10),
		$this->get_rand_color($this->iresource), $this->path_ttf, substr($this->code,1,1));

		imagettftext($this->iresource, rand(25,35), rand(-7,7), 50+rand(-5,5), $ih-10+rand(-5,5),
		$this->get_rand_color($this->iresource), $this->path_ttf, substr($this->code,2,1));

		imagettftext($this->iresource, rand(25,35), rand(-7,7), 70+rand(-5,5), $ih-10+rand(-10,5),
		$this->get_rand_color($this->iresource), $this->path_ttf, substr($this->code,3,1));

		imagettftext($this->iresource, rand(25,35), rand(-7,7), 90+rand(-5,5), $ih-10+rand(-10,5),
		$this->get_rand_color($this->iresource), $this->path_ttf, substr($this->code,4,1));
    }

    /**
     * ���������� ��������� ���� ��� �������� �����.
     *
     * @access private
     * @param $in image resource
     * @return image resource
     */
    private function get_rand_color($in)
    {
        return imagecolorallocate($in, rand(0,128), rand(0,128), rand(0,128));
    }
}
?>
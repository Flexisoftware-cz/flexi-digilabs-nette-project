<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\Config;
use Nette;
use Nette\Utils\Strings;
use function Symfony\Component\String\b;

final class HomePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var array
	 */
	protected array $data;

	/**
	 * @param Config $config
	 */
	public function __construct(protected Config $config)
	{
	}

	/**
	 * @return void
	 */
	protected function startup()
	{
		parent::startup();
		$this->data = $this->downloadData();
	}

	public function renderDefault() {

	}

	public function renderMeme()
	{
		$jokes = [];
		foreach ($this->data as $row) {
			try {
				if (Strings::length($row['joke']) <= 120) {
					$jokes[] = $row['joke'];
				}
			} catch (\Exception $e) {
				continue;
			}
		}
		$text = $jokes[array_rand($jokes)];
		$middle = strrpos(Strings::substring($text, 0, (int) floor(Strings::length($text) / 2)), ' ') + 1;

		$part1 = Strings::upper(Strings::substring($text, 0, $middle));
		$part2 = Strings::upper(Strings::substring($text, $middle));

		$image = Nette\Utils\Image::fromFile('https://www.digilabs.cz/hiring/chuck.jpg');
		$font = Nette\Utils\FileSystem::joinPaths($this->config->getParameter('wwwDir'), 'fonts/Arial_Bold.ttf');
		// text pat one
		$size = (int) (( 100 / Strings::length($part1)) * 11);
		if ($size > 50) {
			$size = 50;
		}
		$calc = Nette\Utils\Image::calculateTextBox($part1, $font, $size);
		$x = (int) (($image->getWidth() - $calc['width']) / 2);
		$y = 70 + $size;
		$image->ttfText($size, 0, $x, $y, Nette\Utils\ImageColor::hex('#FFFFFF'), $font, $part1);
		// text pat two
		$size = (int) (( 100 / Strings::length($part2)) * 11);
		if ($size > 50) {
			$size = 50;
		}
		$calc = Nette\Utils\Image::calculateTextBox($part2, $font, $size);
		$x = (int) (($image->getWidth() - $calc['width']) / 2);
		$y = $image->getHeight() - 60;
		$image->ttfText($size, 0, $x, $y, Nette\Utils\ImageColor::hex('#FFFFFF'), $font, $part2);
		$this->template->image = base64_encode($image->toString());
	}

	public function renderNames()
	{
		$this->template->names = [];
		foreach ($this->data as $row) {
			try {
				$names = explode(' ', $row['name']);
				if (count($names) === 2 && Strings::lower(Strings::substring($names[0], 0, 1)) ===  Strings::lower(Strings::substring($names[1], 0, 1))) {
					$this->template->names[] = $row['name'];
				}
			} catch (\Exception $e) {
				continue;
			}
		}
	}

	public function renderNumbers()
	{
		$this->template->rows = [];
		foreach ($this->data as $row) {
			try {
				if ((int) $row['firstNumber'] / (int) $row['secondNumber'] === (int) $row['thirdNumber'] && (int) $row['firstNumber'] % 2 === 0) {
					$this->template->rows[] = [
						'firstNumber' => $row['firstNumber'],
						'secondNumber' => $row['secondNumber'],
						'thirdNumber' => $row['thirdNumber']
					];
				}
			} catch (\Exception $e) {
				continue;
			}
		}
	}

	public function renderInterval()
	{
		$start = (new \DateTime())->modify('-1 month');
		$end = (new \DateTime())->modify('+1 month');

		$this->template->rows = [];
		foreach ($this->data as $row) {
			try {
				$date = new \DateTime($row['createdAt']);
				if ($date >= $start && $date <= $end) {
					$this->template->rows[] = [
						'createdAt' => $date
					];
				}
			} catch (\Exception $e) {
				continue;
			}
		}
	}

	public function renderCalculation()
	{
		$this->template->calculations = [];
		foreach ($this->data as $row) {
			try {
				$parts = explode('=', $row['calculation']);

				if (count($parts) === 2) {
					if (preg_match_all('/([-0-9]+)/', $parts[0], $matches)) {
						$left = [
							'result' => count($matches[0]) === 1 ? (int) $matches[0][0] : null,
							'numbers' => $matches[1]
						];
						if ($left['result'] === null) {
							if (preg_match_all('/([\+\-\*\/]) /', $parts[0], $matches)) {
								if (count($matches[0]) === count($left['numbers']) - 1) {
									$left['operators'] = $matches[1];

									$result = $left['numbers'][0];
									foreach ($left['operators'] as $key => $operator) {
										switch ($operator) {
											case '+':
												$result += (int) $left['numbers'][$key + 1];
												break;
											case '-':
												$result -= (int) $left['numbers'][$key + 1];
												break;
											case '*':
												$result *= (int) $left['numbers'][$key + 1];
												break;
											case '/':
												$result /= (int) $left['numbers'][$key + 1];
												break;
										}
									}
									$left['result'] = $result;
								}
							}
						}
					}
					if (preg_match_all('/([-0-9]+)/', $parts[1], $matches)) {
						$right = [
							'result' => count($matches[0]) === 1 ? (int) $matches[0][0] : null,
							'numbers' => $matches[1]
						];
						if ($right['result'] === null) {
							if (preg_match_all('/([\+\-\*\/]) /', $parts[1], $matches)) {
								if (count($matches[0]) === count($right['numbers']) - 1) {
									$right['operators'] = $matches[1];

									$result = $right['numbers'][0];
									foreach ($right['operators'] as $key => $operator) {
										switch ($operator) {
											case '+':
												$result += (int) $right['numbers'][$key + 1];
												break;
											case '-':
												$result -= (int) $right['numbers'][$key + 1];
												break;
											case '*':
												$result *= (int) $right['numbers'][$key + 1];
												break;
											case '/':
												$result /= (int) $right['numbers'][$key + 1];
												break;
										}
									}
									$right['result'] = $result;
								}
							}
						}
					}
					if (isset($left) && isset($right) && $left['result'] === $right['result']) {
						$this->template->calculations[] = $row['calculation'];
					}
				}
			} catch (\Exception $e) {
				continue;
			}
		}
	}

	/**
	 * @return array
	 */
	protected function downloadData(): array
	{
		try {
			$file = file_get_contents('https://www.digilabs.cz/hiring/data.php');
			$data = json_decode($file, true);
		} catch (\Exception $e) {
			$data = [];
		}
		return $data;
	}
}

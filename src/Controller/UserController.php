<?php

// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Userf; // Import the userf entity
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory as FakerFactory; // Import the Factory class from the Faker namespace
use Symfony\Component\HttpFoundation\JsonResponse;
use League\Csv\Writer;


class UserController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        // Получаем параметры из запроса
        $region = $request->query->get('region');
        $errorCount = $request->query->get('error_count');
        $seed = $request->query->get('seed');
        $page = $request->query->getInt('page', 1);
        
        $combinedSeed = $this->generateCombinedSeed($seed, $page);
        
        $faker = \Faker\Factory::create();
        $faker->seed($combinedSeed);

       

        // Преобразуем error_count в число с плавающей точкой
        $errorCount = floatval($errorCount);

        // Проверяем, что error_count находится в диапазоне от 0 до 10
        if ($errorCount < 0 || $errorCount > 10) {
            throw new BadRequestHttpException('Error count must be between 0 and 10.');
        }

        // Устанавливаем локаль для Faker в зависимости от выбранного региона
        $locale = $this->getLocaleFromRegion($region);
        $faker = \Faker\Factory::create($locale);

        // Получаем смещение и лимит записей из запроса
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 20);

        // Генерируем фейковые данные с использованием Faker
        $users = [];
        for ($i = $offset; $i < $offset + $limit; $i++) {
            // Генерируем фейковые данные для каждого пользователя
            
            $user = [
                'id' => $faker->randomNumber(),
                'name' => $faker->name(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
            ];


// Вызываем функцию simulateErrors с передачей всех трех аргументов
$user = $this->simulateErrors($user, $errorCount, $locale);
           

            // Добавляем пользователя в массив
            $users[] = $user;
        }

        // Если запрос выполнен через AJAX, возвращаем JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['users' => $users]);
        }

        // Отрисовываем шаблон и передаем в него сгенерированные данные
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'region' => $region,
            'errorCount' => $errorCount,
            'seed' => $seed,
        ]);
    }

    // Функция для определения локали на основе выбранного региона
    private function getLocaleFromRegion($region)
    {
        switch ($region) {
            case 'USA':
                return 'en_US'; // Локаль для английского языка
            case 'Poland':
                return 'pl_PL'; // Локаль для польского языка
            case 'China':
                return 'zh_CN'; // Локаль для китайского языка (мандаринский)
            // Добавьте другие регионы по вашему выбору
           
            case 'Russia':
                return 'ru_RU'; 
           case 'Korea':
                return 'ko_KR';
            case 'Georgia':
                return 'ka_GE';
            default:
                return 'en_US'; // По умолчанию используем английскую локаль
        }
    }

    // Функция для симуляции ошибок в данных
    private function simulateErrors($user, $errorCount, $locale)
    {
        // Создаем экземпляр Faker с указанной локалью
        $faker = \Faker\Factory::create($locale);
    
        // Цикл для симуляции ошибок
        for ($i = 0; $i < $errorCount; $i++) {
            // Случайный выбор поля для ошибки (имя, адрес или телефон)
            $field = mt_rand(0, 2);
    
            // Выполнение соответствующей ошибки в выбранном поле
            switch ($field) {
                case 0:
                    // Ошибка в имени
                    if (!empty($user['name'])) {
                        $user['name'] = $this->introduceError($user['name'], $faker);
                    }
                    break;
                case 1:
                    // Ошибка в адресе
                    if (!empty($user['address'])) {
                        $user['address'] = $this->introduceError($user['address'], $faker);
                    }
                    break;
                case 2:
                    // Ошибка в телефоне
                    if (!empty($user['phone'])) {
                        $user['phone'] = $this->introduceError($user['phone'], $faker);
                    }
                    break;
            }
        }
    
        return $user;
    }
    
    private function introduceError($value, $faker)
{
    // Случайный выбор типа ошибки (удаление, добавление или перестановка символов)
    $errorType = mt_rand(0, 2);

    // Выполнение соответствующей ошибки
    switch ($errorType) {
        case 0:
            // Удаление одного случайного символа
            $randomIndex = mt_rand(0, mb_strlen($value, 'UTF-8') - 1);
            $value = mb_substr($value, 0, $randomIndex) . mb_substr($value, $randomIndex + 1);
            break;
        case 1:
            // Добавление одного случайного символа
            $randomChar = $faker->randomLetter();
            $randomIndex = mt_rand(0, mb_strlen($value, 'UTF-8'));
            $value = mb_substr($value, 0, $randomIndex) . $randomChar . mb_substr($value, $randomIndex);
            break;
        case 2:
            // Перестановка двух соседних символов местами
            $valueLength = mb_strlen($value, 'UTF-8');
            if ($valueLength >= 2) {
                $randomIndex = mt_rand(0, $valueLength - 2);
                $char1 = mb_substr($value, $randomIndex, 1);
                $char2 = mb_substr($value, $randomIndex + 1, 1);
                $value = mb_substr($value, 0, $randomIndex) . $char2 . $char1 . mb_substr($value, $randomIndex + 2);
            }
            break;
    }

    return $value;
}
private function generateCombinedSeed($seed, $page)
{
    // Преобразуем значение сида и номер страницы в целые числа
    $seed = intval($seed);
    $page = intval($page);

    // Комбинируем сид и номер страницы, например, путем сложения
    $combinedSeed = $seed + $page;

    return $combinedSeed;
}
/**
 * @Route("/export-to-csv", name="export_to_csv")
 */
public function exportToCsv(Request $request): Response
    {
        // Получаем данные, переданные с клиентской части
        $userData = json_decode($request->getContent(), true);

        // Создаем объект Writer для создания CSV-файла
        $csvWriter = Writer::createFromString('');

        // Записываем данные в CSV-файл
        foreach ($userData as $user) {
            $csvWriter->insertOne([$user['id'], $user['name'], $user['address'], $user['phone']]);
        }

        // Устанавливаем заголовки для CSV-файла
        $response = new Response($csvWriter->getContent(), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users.csv"');

        return $response;
    }
}
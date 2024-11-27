<?php
$id = htmlspecialchars($_GET['id']);


include_once($_SERVER['DOCUMENT_ROOT'] . "/utils/functions.php");
$reports = query("SELECT reports.*, users.name as user_name FROM reports
                    JOIN users ON reports.user_id = users.id  WHERE reports.id = '$id' ORDER BY created_at ASC");
header('refresh:3;Content-Type: text/html; charset=UTF-8');

$report_unsolved = query("SELECT  COUNT(status) as status FROM reports WHERE status = 0");

?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
    <div class="col-span-3 grid grid-cols-1 gap-4">
        <!-- Card 1 -->
        <?php
        if (empty($reports)) : ?>
            <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-400 via-blue-700 to-blue-900 text-transparent bg-clip-text">Data kosong</h1>
        <?php else : ?>
            <?php
            foreach ($reports as $report) :
                $report['content'] = html_entity_decode(html_entity_decode(strval($report['content'])));

            ?>

                <div class="bg-white border rounded-xl shadow-sm sm:flex bg-gradient-to-br from-gray-800 to-gray-900">
                    <div class="shrink-0 relative w-full rounded-t-xl overflow-hidden pt-[40%] sm:rounded-s-xl sm:max-w-60 md:rounded-se-none md:max-w-xs">
                        <?php if ($report['thumbnail']): ?>
                            <img class="size-full absolute top-0 start-0 object-cover" alt="<?= $report['thumbnail'] ?>" src="<?= "../assets/upload/" . $report['thumbnail'] ?>">
                        <?php endif ?>
                    </div>
                    <div class="flex flex-wrap">
                        <div class="p-4 flex flex-col h-full sm:p-7">
                            <h3 class="text-3xl drop-shadow-md font-bold bg-gradient-to-r from-blue-400 via-blue-700 to-blue-900 text-transparent bg-clip-text">
                                <span class="text-white font-bold text-3xl">Title :</span> <?= $report['title'] ?>
                            </h3>
                            <h4 class="mt-1 text-white dark:text-white">
                                <span class="text-white font-bold text-xl">Content :</span> <?= $report['content'] ?>
                            </h4>
                            <div class="mt-5 sm:mt-auto">
                                <p class="text-sm font-medium text-white truncate"> <span class="text-white font-bold text-lg">user :</span> <?= $report['user_name'] ?></p>
                                <p class="text-xs text-gray-500 truncate dark:text-gray-400"> <span class="text-white font-bold text-lg">date :</span> <?= $report['created_at'] ?></p>
                            </div>
                            <a href="index.php?page=reports&id=<?= $report['id'] ?>" class="block text-white bg-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-5" type="button">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif ?>

        <div class="w-full bg-white rounded-lg border p-2 my-4 ">
            <h3 class="font-bold">Comment</h3>
            <form>
                <div class="flex flex-col">
                    <div class="border rounded-md p-3 ml-3 my-3">
                        <div class="flex gap-3 items-center">
                            <img src="/assets/images/logo qindev.png"
                                class="object-cover w-8 h-8 rounded-full 
                                        border-2 border-blue-600  shadow-blue-400
                                        ">
                            <h3 class="font-bold">
                                Super Admin
                            </h3>
                        </div>
                        <p class="text-gray-600 mt-2">
                            Bagus Laporan Nya 😍
                        </p>
                    </div>

                    <div class="border rounded-md p-3 ml-3 my-3">
                        <div class="flex gap-3 items-center">
                            <img src="/assets/images//logo gue.png"
                                class="object-cover w-8 h-8 rounded-full 
                                        border-2 border-emerald-400  shadow-emerald-400
                                        ">
                            <h3 class="font-bold">
                                Direktur Utama
                            </h3>
                        </div>
                        <p class="text-gray-600 mt-2">
                            Tambah Gajinya 💰
                        </p>
                    </div>
                </div>

                <div class="w-full px-3 my-2">
                    <textarea
                        class="bg-gray-100 rounded border border-gray-400 leading-normal resize-none w-full h-20 py-2 px-3 font-medium placeholder-gray-700 focus:outline-none focus:bg-white"
                        name="body" placeholder='Type Your Comment' required></textarea>
                </div>

                <div class="w-full flex justify-end px-3">
                    <input type='submit' class="px-2.5 py-1.5 rounded-md text-white text-sm bg-blue-600" value='Comment'>
                </div>
            </form>
        </div>

    </div>

    <div class="relative flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md">
        <div class="bg-clip-border mx-4 rounded-xl overflow-hidden bg-gradient-to-tr from-blue-600 to-blue-400 text-white shadow-blue-500/40 shadow-lg absolute -mt-0 grid h-16 w-16 place-items-center">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6H5m2 3H5m2 3H5m2 3H5m2 3H5m11-1a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2M7 3h11a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm8 7a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
            </svg>
        </div>
        <div class="p-4 text-right">
            <p class="block antialiased font-sans text-sm leading-normal font-normal text-blue-gray-600">Jumlah laporan</p>
            <h4 class="block antialiased tracking-normal font-sans text-2xl font-semibold leading-snug text-blue-gray-900">
                <?= $report_unsolved[0]['status'] ?>

            </h4>
        </div>
        <div class="border-t border-blue-gray-50 p-4">
            <p class="block antialiased font-sans text-base leading-relaxed font-normal text-blue-gray-600">
                <strong class="text-green-500">Laporan</strong>&nbsp;/ HARI
            </p>
        </div>
    </div>

</div>
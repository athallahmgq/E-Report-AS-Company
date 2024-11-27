<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
    <div class="border-2 col-span-1 md:col-span-4 rounded-lg border-blue-600 dark:border-blue-600 h-auto mb-4">
        <form class="py-5 px-6" method="post" enctype="multipart/form-data">
            <div class="space-y-12">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-5xl font-bold bg-gradient-to-r from-blue-500 via-blue-700 to-blue-900 text-transparent bg-clip-text">Report</h2>
                    <p class="mt-1 text-sm/6 text-gray-600">This information will be displayed publicly so be careful what you share.</p>

                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="col-span-full">
                            <label for="title" class="block text-sm font-medium text-gray-900">Title</label>
                            <div class="mt-2">
                                <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 w-full">
                                    <input type="text" name="title" id="title" class="ml-4 block w-full border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" placeholder="janesmith">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-full">
                            <label for="content" class="block text-sm/6 font-medium text-gray-900">Content</label>
                            <div class="mt-2">
                                <textarea id="content" name="content" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6"></textarea>
                            </div>
                            <p class="mt-3 text-sm/6 text-gray-600">Write a few sentences about yourself.</p>
                        </div>

                        <div class="col-span-full">
                            <label for="thumbnail" class="block text-sm/6 font-medium text-gray-900">Thumbnail</label>
                            <input id="thumbnail" name="thumbnail" type="file" accept="image/png, image/jpeg, image/jpg">
                        </div>

                    </div>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm/6 font-semibold text-gray-900" onclick="window.history.back()">Cancel</button>
                <button name="submit" type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
            </div>
        </form>

    </div>

</div>


<?php
if (isset($_POST['submit'])) {
    handleFormSubmit($_POST, 'reports', 'add');
}

?>
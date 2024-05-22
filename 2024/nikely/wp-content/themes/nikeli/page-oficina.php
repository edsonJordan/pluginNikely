<?php
include_once get_stylesheet_directory() . '/includes/OrderPoints.php';
OrderPoints::initialize();

// Obtener los datos necesarios para el gráfico
$data = OrderPoints::getChartData();
// var_dump($data);
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

   <link rel="stylesheet" href="<?= get_stylesheet_directory_uri() ?>/sources/style.css">
</head>

<body>


   <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
      <div class="px-3 py-3 lg:px-5 lg:pl-3">
         <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
               <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                  <span class="sr-only">Open sidebar</span>

               </button>
               <a href="#" class="flex ms-2 md:me-24">
                  <img src="<?= get_stylesheet_directory_uri() ?>/sources/img/logo.jpg" class="h-8 me-3" alt="Nikeli Logo" />
                  <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Nikeli</span>
               </a>
            </div>
            <div class="flex items-center">
               <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                  <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                     <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                  </svg>
                  <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                     <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                  </svg>
               </button>
               <div class="flex items-center ms-3">

                  <div>
                     <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                     </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                     <div class="px-4 py-3" role="none">
                        <p class="text-sm text-gray-900 dark:text-white" role="none">
                           Neil Sims
                        </p>
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                           web@gato.pe
                        </p>
                     </div>
                     <ul class="py-1" role="none">
                        <li>
                           <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Dashboard</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Settings</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Earnings</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Sign out</a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </nav>


   <?php get_template_part('templates/sidebar');
   ?>




   <div class="p-0 md:p-4 xl:p-8  bg-gray-100 dark:bg-gray-900 sm:ml-64">
      <div class="p-8 bg-white dark:bg-gray-600 shadow-lg  rounded-lg dark:border-gray-700 mt-14">
         <div class="w-full  grid   grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-8 mb-4">


            <!-- Chart -->
            <div id="chart-container1" class="chart-container  w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
               <div class="flex justify-between">
                  <div>
                     <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">32.4k</h5>
                     <p class="text-base font-normal text-gray-500 dark:text-gray-400">Puntos generados</p>
                  </div>
                  <div class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
                     12%
                     <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                     </svg>
                  </div>
               </div>
               <div id="chart-puntos-generados" class="graphic"></div>
               <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                  <div class="flex justify-between items-center pt-5">
                     <!-- Button -->
                     <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown1" data-dropdown-placement="bottom" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white" type="button">
                        Ultimos 90 dias
                        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                     </button>
                     <!-- Dropdown menu -->
                     <div id="lastDaysdropdown1" class="dropdown-button  z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                           <li>
                              <a href="#" data-timeframe="yesterday" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ayer</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="today" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hoy</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last7days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 7 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last30days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 30 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last90days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 90 dias</a>
                           </li>
                        </ul>
                     </div>

                    
                  </div>
               </div>
            </div>


            <div id="chart-container2" class="chart-container  w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
               <div class="flex justify-between">
                  <div>
                     <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">32.4k</h5>
                     <p class="text-base font-normal text-gray-500 dark:text-gray-400">Ganancias generadas</p>
                  </div>
                  <div class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
                     12%
                     <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                     </svg>
                  </div>
               </div>
               <div id="chart-puntos-generados" class="graphic"></div>
               <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                  <div class="flex justify-between items-center pt-5">
                     <!-- Button -->
                     <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown2" data-dropdown-placement="bottom" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white" type="button">
                        Ultimos 90 dias
                        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                     </button>
                     <!-- Dropdown menu -->
                     <div id="lastDaysdropdown2" class="dropdown-button  z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                           <li>
                              <a href="#" data-timeframe="yesterday" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ayer</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="today" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hoy</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last7days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 7 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last30days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 30 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last90days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 90 dias</a>
                           </li>
                        </ul>
                     </div>

                  </div>
               </div>
            </div>
            <div id="chart-container3" class="chart-container  w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
               <div class="flex justify-between">
                  <div>
                     <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">32.4k</h5>
                     <p class="text-base font-normal text-gray-500 dark:text-gray-400">Referidos</p>
                  </div>
                  <div class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
                     12%
                     <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                     </svg>
                  </div>
               </div>
               <div id="chart-puntos-generados" class="graphic"></div>
               <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                  <div class="flex justify-between items-center pt-5">
                     <!-- Button -->
                     <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown3" data-dropdown-placement="bottom" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white" type="button">
                        Ultimos 90 dias
                        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                           <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                     </button>
                     <!-- Dropdown menu -->
                     <div id="lastDaysdropdown3" class="dropdown-button  z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                           <li>
                              <a href="#" data-timeframe="yesterday" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ayer</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="today" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hoy</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last7days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 7 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last30days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 30 dias</a>
                           </li>
                           <li>
                              <a href="#" data-timeframe="last90days" class="timeframe-option block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Ultimos 90 dias</a>
                           </li>
                        </ul>
                     </div>

                  </div>
               </div>
            </div>

         </div>

         <div class="w-full grid   grid-cols-1 bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
            <h3 class="leading-5 font-bold text-sm text-gray-900 py-8">Pedidos de mi referidos</h3>
            <div class="flex justify-end mb-5">


               <div>
                  <button id="dropdownDefaultButton" data-dropdown-toggle="lastDaysdropdown" data-dropdown-placement="bottom" type="button" class="px-3 py-2 inline-flex items-center text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Last
                     week <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                     </svg></button>
                  <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                     <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                        <li>
                           <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                              7 days</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                              30 days</a>
                        </li>
                        <li>
                           <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last
                              90 days</a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <div id="line-chart"></div>
            <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-2.5">
            </div>
         </div>


      </div>
   </div>
   </div>




</body>
<script>
   // On page load or when changing themes, best to add inline in `head` to avoid FOUC
   if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
   } else {
      document.documentElement.classList.remove('dark')
   }
</script>
<script>
   var chartData = <?php echo json_encode($data); ?>;
</script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/sources/apexcharts.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/sources/chart.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/sources/theme-mode.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>



</html>
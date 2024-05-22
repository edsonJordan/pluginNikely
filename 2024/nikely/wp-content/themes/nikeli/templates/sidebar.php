<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
     <div class="content content__aside">
        <ul class="space-y-2 font-medium flex flex-col gap-y-1">
           <li>
              <a href="#" class="aside__link group">
                     <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/chart-pie.svg#icon-shart"></use>
                     </svg>                
                 <span class="ms-3">Estadísticas</span>
              </a>
           </li>
           <li>
            <a href="#" class="aside__link group">
               <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                  <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/globe.svg#icon-globe"></use>
               </svg> 
               <span class="aside__link-text">Información General</span>
            </a>
         </li>
         <li>
               <button type="button" class="group link__dropdown" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                  <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                     <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/sale-percent.svg#icon-percent"></use>
                  </svg>  
                     <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Descuentos</span>
                     <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                     </svg>
               </button>
               <ul id="dropdown-example" class="hidden py-2 space-y-2">
                     <li>
                        <a href="#" class="dropdown__link group">Decuentos Exclusivos</a>
                     </li>
                     <li>
                        <a href="#" class="dropdown__link group">Recompensas</a>
                     </li>
                     <li>
                        <a href="#" class="dropdown__link group">Cupones Exclusivos</a>
                     </li>
               </ul>
         </li>
         <li>
            <a href="#" class="aside__link group">
               <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                  <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/truck.svg#icon-truck"></use>
               </svg> 
               <span class="aside__link-text">Pedidos</span>
               <span class="link__alert">3</span>
            </a>
         </li>
         <li>
               <button type="button" class="group link__dropdown" aria-controls="dropdown-productos" data-collapse-toggle="dropdown-productos">
                  <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                     <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/bag.svg#icon-bag"></use>
                  </svg>  
                     <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Productos</span>
                     <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                     </svg>
               </button>
               <ul id="dropdown-productos" class="hidden py-2 space-y-2">
                     <li>
                        <a href="#" class="dropdown__link group">Productos mejor valorados</a>
                     </li>
                     <li>
                        <a href="#" class="dropdown__link group">Productos con descuento</a>
                     </li>
               </ul>
         </li>
           <li>
              <a href="#" class="aside__link group">
                  <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                     <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/rocket.svg#icon-rocket"></use>
                  </svg> 
                 <span class="aside__link-text">Beneficios</span>
                 <span class="link__alert">3</span>
              </a>
           </li>
         <li>
               <button type="button" class="group link__dropdown " aria-controls="dropdown-perfil" data-collapse-toggle="dropdown-perfil">
                  <svg class="link__icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                     <use xlink:href="<?= get_stylesheet_directory_uri()?>/sources/icons/user.svg#icon-user"></use>
                  </svg>  
                     <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Perfil</span>
                     <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                     </svg>
               </button>
               <ul id="dropdown-perfil" class="hidden py-2 space-y-2">
                     <li>
                        <a href="#" class="dropdown__link group">Información</a>
                     </li>
                     <li>
                        <a href="#" class="dropdown__link group">Cambiar Contraseña</a>
                     </li>
               </ul>
         </li>
          
        </ul>
     </div>
</aside>
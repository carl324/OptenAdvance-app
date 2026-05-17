

<?php $__env->startSection('title', 'Ajustes'); ?>

<?php $__env->startSection('content'); ?>


   
      <!-- ========== signin-section start ========== -->
      <section class="signin-section">
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              
              
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <!-- ========== title-wrapper end ========== -->

          <div class="row g-0 auth-row">
            <div class="col-lg-6">
              <div class="auth-cover-wrapper bg-primary-100">
                <div class="auth-cover">
                  
                  <div class="cover-image">
                    <img src="assets/images/cards/ajustes.png" alt="" style="width: 100%; height: auto;" />
                  </div>
                  <div class="shape-image">
                    <img src="assets/images/auth/shape.svg" alt="" />
                  </div>
                </div>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-6">
  <div class="signin-wrapper">
    <div class="form-wrapper">
      <h6 class="mb-10">Ajustes</h6>
      <p class="text-sm mb-30 text-gray">Administra la configuración de tu sistema</p>

      <div class="d-flex flex-column gap-2">

        <!-- Empresa -->
        <a href="<?php echo e(route('empresa.index')); ?>" class="text-decoration-none">
          <div class="d-flex align-items-center gap-3 p-3" style="border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.2s; cursor: pointer;"
               onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 4px 20px rgba(59,130,246,0.10)'"
               onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <i class="lni lni-briefcase" style="font-size:19px; color:#3b82f6;"></i>
            </div>
            <div>
              <div style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:1px;">Empresa</div>
              <div style="font-size:12px; color:#64748b;">Nombre, NIT, dirección y datos fiscales</div>
            </div>
            <i class="lni lni-chevron-right ms-auto" style="color:#94a3b8;"></i>
          </div>
        </a>

        <!-- Base de datos -->
        <a href="<?php echo e(route('db.index')); ?>" class="text-decoration-none">
  <div class="d-flex align-items-center gap-3 p-3" 
       style="border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.2s; cursor: pointer;"
       onmouseover="this.style.borderColor='#ec4899'; this.style.boxShadow='0 4px 20px rgba(236,72,153,0.15)'"
       onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
    
    <div style="width:40px; height:40px; background:#fdf2f8; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
      <i class="lni lni-database" style="font-size:19px; color:#ec4899;"></i>
    </div>

    <div>
      <div style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:1px;">Base de datos</div>
      <div style="font-size:12px; color:#64748b;">Respaldos, restauración y exportación</div>
    </div>

    <i class="lni lni-chevron-right ms-auto" style="color:#94a3b8;"></i>
  </div>
</a>

        <!-- Licencia -->
        <a href="<?php echo e(route('licencia.index')); ?>" class="text-decoration-none">
          <div class="d-flex align-items-center gap-3 p-3" style="border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.2s; cursor: pointer;"
               onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 4px 20px rgba(139,92,246,0.10)'"
               onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; background:#f5f3ff; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <i class="lni lni-certificate" style="font-size:19px; color:#8b5cf6;"></i>
            </div>
            <div>
              <div style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:1px;">Licencia</div>
              <div style="font-size:12px; color:#64748b;">Estado, vencimiento y activación</div>
            </div>
            <i class="lni lni-chevron-right ms-auto" style="color:#94a3b8;"></i>
          </div>
        </a>

        <!-- Auditoría -->
        <a href="<?php echo e(route('auditoria.index')); ?>" class="text-decoration-none">
          <div class="d-flex align-items-center gap-3 p-3" style="border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.2s; cursor: pointer;"
               onmouseover="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 4px 20px rgba(245,158,11,0.10)'"
               onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; background:#fffbeb; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <i class="lni lni-search-alt" style="font-size:19px; color:#f59e0b;"></i>
            </div>
            <div>
              <div style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:1px;">Auditoría</div>
              <div style="font-size:12px; color:#64748b;">Registro de actividad y cambios del sistema</div>
            </div>
            <i class="lni lni-chevron-right ms-auto" style="color:#94a3b8;"></i>
          </div>
        </a>
        <!-- Devoluciones -->
<a href="<?php echo e(route('configuracion.devoluciones')); ?>" class="text-decoration-none">
  <div class="d-flex align-items-center gap-3 p-3" style="border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; transition: all 0.2s; cursor: pointer;"
       onmouseover="this.style.borderColor='#10b981'; this.style.boxShadow='0 4px 20px rgba(16,185,129,0.10)'"
       onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
    
    <div style="width:40px; height:40px; background:#ecfdf5; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
      <i class="lni lni-reload" style="font-size:19px; color:#10b981;"></i>
    </div>

    <div>
      <div style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:1px;">Devoluciones</div>
      <div style="font-size:12px; color:#64748b;">Días permitidos y motivos de devolución</div>
    </div>

    <i class="lni lni-chevron-right ms-auto" style="color:#94a3b8;"></i>
  </div>
</a>

      </div>
    </div>
  </div>
</div>
            <!-- end col -->
          </div>
          <!-- end row -->
        </div>
      </section>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\ajustes\index.blade.php ENDPATH**/ ?>
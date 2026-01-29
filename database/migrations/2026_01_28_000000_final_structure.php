<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cache tables
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Jobs & related
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable()->unique();
            $table->string('password');
            $table->string('role')->default('vendedor');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->tinyInteger('activo')->default(1);
            $table->timestamps();
            // composite unique will be created after table definition to match later migration behavior
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email', 'activo']);
        });

        // Empresa
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique()->nullable();
            $table->string('nit', 50)->unique()->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('moneda', 10)->default('COP')->nullable();
            $table->boolean('cobra_iva')->nullable();
            $table->timestamps();
        });

        // Productos
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->decimal('precio', 10, 2);
            $table->decimal('iva', 5, 2)->default(0);
            $table->decimal('precio_con_iva', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Cajas
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('fecha_apertura');
            $table->decimal('monto_apertura', 12, 2);
            $table->string('nota_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('total_ventas', 12, 2)->nullable();
            $table->decimal('total_efectivo', 12, 2)->nullable();
            $table->decimal('monto_cierre_calculado', 12, 2)->nullable();
            $table->decimal('monto_cierre_real', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->string('nota_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada']);
            $table->timestamps();
        });

        // Ventas
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente', 100)->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['completada', 'anulada'])->default('completada');
            $table->dateTime('fecha');
            $table->timestamps();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
        });

        // Ventas detalle
        Schema::create('ventas_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0.00);
        });

        // Facturas
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->dateTime('fecha_emision');
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_nit')->nullable();
            $table->decimal('total', 12, 2);
            $table->decimal('impuestos', 12, 2);
            $table->string('forma_pago');
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Inventario movimientos
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida']);
            $table->integer('cantidad');
            $table->enum('origen', ['registro_producto', 'venta', 'venta_anulada', 'ajuste']);
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('descripcion')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('ventas_detalle');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('cajas');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('empresa');
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'activo']);
        });
        Schema::dropIfExists('users');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('
            CREATE TABLE inventario_movimientos_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                producto_id INTEGER NOT NULL,
                tipo TEXT CHECK(tipo IN ("entrada","salida")) NOT NULL,
                cantidad INTEGER NOT NULL,
                origen TEXT CHECK(origen IN (
                    "registro_producto",
                    "venta",
                    "venta_anulada",
                    "ajuste"
                )) NOT NULL,
                referencia_id INTEGER,
                descripcion TEXT,
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(producto_id) REFERENCES productos(id)
            )
        ');

        DB::statement('
            INSERT INTO inventario_movimientos_new
            SELECT * FROM inventario_movimientos
        ');

        DB::statement('DROP TABLE inventario_movimientos');
        DB::statement('ALTER TABLE inventario_movimientos_new RENAME TO inventario_movimientos');
    }

    public function down(): void
    {
        // No rollback seguro en SQLite para CHECKs
    }
};

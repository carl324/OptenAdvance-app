<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Empresa;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('tipo', 'ventas');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        $empresa = Empresa::first();

        // Build base query with filters but DO NOT execute it here
        $query = $this->buildReporteQuery($request, $tipo);

        // For screen display we paginate (keeps UI responsive)
        $data = $query->paginate(15)->withQueryString();

        return view('reportes.index', compact('empresa', 'tipo', 'fecha_inicio', 'fecha_fin', 'data'));
    }

    public function export(Request $request)
    {
        // Keep existing route but return Excel using the new exportExcel method
        return $this->exportExcel($request);
    }


    /**
     * Exporta todas las ventas filtradas a un archivo XLSX usando PhpSpreadsheet.
     * No usa la vista ni HTML; descarga directa.
     */
    public function exportExcel(Request $request)
    {
        $empresa = Empresa::first();

        // Build query for ventas with filters (but do not paginate)
        $query = $this->buildReporteQuery($request, 'ventas');
        $ventas = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Ventas');

        // Headers
        $headers = ['Fecha', 'Número factura', 'Cliente', 'Subtotal', 'IVA', 'Total', 'Forma de pago', 'Estado'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $row = 2;
        $totalSubtotal = 0;
        $totalIva = 0;
        $totalTotal = 0;

        foreach ($ventas as $v) {
            $factura = $v->factura ?? null;
            $fecha = optional($v->fecha)->format('Y-m-d H:i');
            $numero = $factura->numero ?? '-';
            $cliente = $factura->cliente_nombre ?? $v->cliente ?? '-';
            $iva = $factura->impuestos ?? 0;
            $total = $factura->total ?? $v->total ?? 0;
            $subtotal = $total - ($iva ?? 0);
            $forma = $factura->forma_pago ?? '-';
            $estado = $v->estado ?? '-';

            $sheet->setCellValue('A' . $row, $fecha);
            $sheet->setCellValue('B' . $row, $numero);
            $sheet->setCellValue('C' . $row, $cliente);

            // Numeric values as numbers
            $sheet->setCellValue('D' . $row, (float)$subtotal);
            $sheet->setCellValue('E' . $row, (float)$iva);
            $sheet->setCellValue('F' . $row, (float)$total);

            $sheet->setCellValue('G' . $row, $forma);
            $sheet->setCellValue('H' . $row, $estado);

            $totalSubtotal += (float)$subtotal;
            $totalIva += (float)$iva;
            $totalTotal += (float)$total;

            $row++;
        }

        // Totals row
        $sheet->setCellValue('C' . $row, 'TOTALES');
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('D' . $row, $totalSubtotal);
        $sheet->setCellValue('E' . $row, $totalIva);
        $sheet->setCellValue('F' . $row, $totalTotal);
        $sheet->getStyle('D' . $row . ':F' . $row)->getFont()->setBold(true);

        // Format numeric columns as currency
        $currencyFormat = '"$"#,##0';
        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('F2:F' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        // Auto size columns
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'reporte_ventas_' . now()->format('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }


    /**
     * Construye la query base del reporte según filtros y tipo.
     * NO ejecuta la consulta: devuelve un Eloquent Builder listo para ->paginate() o ->get().
     *
     * @param Request $request
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildReporteQuery(Request $request, string $tipo)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $estado = $request->input('estado');
        $order = strtolower($request->input('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $start = $fecha_inicio ? Carbon::parse($fecha_inicio)->startOfDay() : null;
        $end = $fecha_fin ? Carbon::parse($fecha_fin)->endOfDay() : null;

        if ($tipo === 'ventas') {
            // Eloquent builder with factura eager loaded
            $q = Venta::with('factura')->select('ventas.*');
            if ($start) $q->where('fecha', '>=', $start);
            if ($end) $q->where('fecha', '<=', $end);
            if ($estado) $q->where('estado', $estado);
            $q->orderBy('fecha', $order);
            return $q;

        } elseif ($tipo === 'ventas_detalle') {
            // Join ventas so we can order by venta.fecha and also eager load relations
            $q = VentaDetalle::with('producto', 'venta')
                ->select('ventas_detalle.*')
                ->join('ventas', 'ventas.id', '=', 'ventas_detalle.venta_id');

            if ($start) $q->where('ventas.fecha', '>=', $start);
            if ($end) $q->where('ventas.fecha', '<=', $end);
            if ($estado) $q->where('ventas.estado', $estado);

            $q->orderBy('ventas.fecha', $order);
            return $q;

        } else {
            // inventario
            $q = Producto::query();
            $q->orderBy('nombre', $order === 'asc' ? 'asc' : 'desc');
            return $q;
        }
    }
}

@extends('layouts.student')

@section('title', 'Unduh Dokumen')
@section('page_title', 'Unduh Dokumen')

@section('styles')
<style>
    .doc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .doc-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        transition: box-shadow 0.3s;
    }
    .doc-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .doc-card .doc-header {
        padding: 20px 24px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .doc-card .doc-header h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .doc-card .doc-header h3.skl i { color: #4f46e5; }
    .doc-card .doc-header h3.transkrip i { color: #059669; }

    .doc-card .doc-preview {
        padding: 16px 24px 20px;
    }
    .doc-card .doc-preview iframe {
        width: 100%;
        height: 500px;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        background: #fafbfc;
    }

    .doc-card .doc-actions {
        padding: 0 24px 20px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn-doc {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
        transition: all 0.25s;
        border: none;
        cursor: pointer;
    }
    .btn-doc:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .btn-doc.skl {
        background: linear-gradient(135deg, #4f46e5, #818cf8);
    }
    .btn-doc.transkrip {
        background: linear-gradient(135deg, #059669, #34d399);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
    }
    .empty-state i {
        font-size: 3rem;
        color: #e2e8f0;
        margin-bottom: 16px;
    }
    .empty-state h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .empty-state p {
        font-size: 0.88rem;
        color: #94a3b8;
        margin: 0;
    }

    @media (max-width: 768px) {
        .doc-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="fade-in">
    @if($student->status !== 'LULUS')
        <div class="empty-state">
            <i class="fa-solid fa-file-lock"></i>
            <h3>Dokumen Belum Tersedia</h3>
            <p>Dokumen SKL dan Transkrip hanya tersedia untuk siswa yang telah dinyatakan <strong>LULUS</strong>.</p>
        </div>
    @else
        <div class="doc-grid">
            <div class="doc-card">
                <div class="doc-header">
                    <h3 class="skl"><i class="fa-solid fa-file-signature"></i> Surat Keterangan Lulus (SKL)</h3>
                </div>
                <div class="doc-preview">
                    <iframe src="{{ route('student.preview.skl') }}" title="Preview SKL"></iframe>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.preview.skl') }}" class="btn-doc skl" target="_blank">
                        <i class="fa-solid fa-eye"></i> Lihat PDF
                    </a>
                    <a href="{{ route('public.skl.pdf', $student->id) }}" class="btn-doc skl">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                </div>
            </div>

            <div class="doc-card">
                <div class="doc-header">
                    <h3 class="transkrip"><i class="fa-solid fa-file-invoice"></i> Transkrip Nilai Kelulusan</h3>
                </div>
                <div class="doc-preview">
                    <iframe src="{{ route('student.preview.transcript') }}" title="Preview Transkrip"></iframe>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.preview.transcript') }}" class="btn-doc transkrip" target="_blank">
                        <i class="fa-solid fa-eye"></i> Lihat PDF
                    </a>
                    <a href="{{ route('public.transcript.pdf', $student->id) }}" class="btn-doc transkrip">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                </div>
            </div>
            </div>
        </div>
    @endif
</div>
@endsection

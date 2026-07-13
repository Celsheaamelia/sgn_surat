<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TujuanSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tujuan_surats')->upsert([
            [
                'kode' => 'BD00',
                'nama_tujuan' => 'SELURUH DIREKSI',
            ],
            [
                'kode' => 'BD01',
                'nama_tujuan' => 'DIREKTUR UTAMA',
            ],
            [
                'kode' => 'BD02',
                'nama_tujuan' => 'DIREKTUR OPERASIONAL',
            ],
            [
                'kode' => 'BD03',
                'nama_tujuan' => 'DIREKTUR KEUANGAN',
            ],
            [
                'kode' => 'BD04',
                'nama_tujuan' => 'DIREKTUR HK & MR',
            ],
            [
                'kode' => 'BD05',
                'nama_tujuan' => 'DIREKTUR SDM & IT',
            ],
            [
                'kode' => 'SV01',
                'nama_tujuan' => 'SEVP STRATEGIC BUSINESS',
            ],
            [
                'kode' => 'SV02',
                'nama_tujuan' => 'SEVP OPERATION',
            ],
            [
                'kode' => 'SV03',
                'nama_tujuan' => 'SEVP SDM & TEKNOLOGI INFORMASI',
            ],
            [
                'kode' => 'KP00',
                'nama_tujuan' => 'SELURUH DIVISI',
            ],
            [
                'kode' => 'KP01',
                'nama_tujuan' => 'DIVISI SEKRETARIAT PERUSAHAAN',
            ],
            [
                'kode' => 'KP02',
                'nama_tujuan' => 'DIVISI SATUAN PENGAWASAN INTERN',
            ],
            [
                'kode' => 'KP03',
                'nama_tujuan' => 'DIVISI STRATEGI BISNIS & KEBERLANJUTAN',
            ],
            [
                'kode' => 'KP04',
                'nama_tujuan' => 'DIVISI PENELITIAN & PENGEMBANGAN',
            ],
            [
                'kode' => 'KP06',
                'nama_tujuan' => 'DIVISI TEKNIK & PENGOLAHAN',
            ],
            [
                'kode' => 'KP07',
                'nama_tujuan' => 'DIVISI BUDIDAYA',
            ],
            [
                'kode' => 'KP08',
                'nama_tujuan' => 'DIVISI QUALITY ASSURANCE',
            ],
            [
                'kode' => 'KP09',
                'nama_tujuan' => 'DIVISI OPERASIONAL SDM',
            ],
            [
                'kode' => 'KP10',
                'nama_tujuan' => 'DIVISI TEKNOLOGI INFORMASI',
            ],
            [
                'kode' => 'KP11',
                'nama_tujuan' => 'DIVISI HUBUNGAN KELEMBAGAAN & HUKUM',
            ],
            [
                'kode' => 'KP12',
                'nama_tujuan' => 'DIVISI PEMASARAN & PELANGGAN',
            ],
            [
                'kode' => 'KP13',
                'nama_tujuan' => 'DIVISI TR & TMA',
            ],
            [
                'kode' => 'KP14',
                'nama_tujuan' => 'DIVISI AKUNTANSI & PERPAJAKAN',
            ],
            [
                'kode' => 'KP15',
                'nama_tujuan' => 'DIVISI PENGADAAN',
            ],
            [
                'kode' => 'KP16',
                'nama_tujuan' => 'DIVISI PERBENDAHARAAN & ANGGARAN',
            ],
            [
                'kode' => 'KP17',
                'nama_tujuan' => 'DIVISI MANAJEMEN RISIKO',
            ],
            [
                'kode' => 'KP18',
                'nama_tujuan' => 'DIVISI PENGEMBANGAN SDM DAN BUDAYA',
            ],
            [
                'kode' => 'KP00X',
                'nama_tujuan' => 'SELURUH SUB DIVISI',
            ],
            [
                'kode' => 'KP01A',
                'nama_tujuan' => 'SUB DIVISI KESEKRETARIATAN, PENGELOLAAN DATA DAN INFORMASI',
            ],
            [
                'kode' => 'KP01B',
                'nama_tujuan' => 'SUB DIVISI HUMAS DAN PROTOKOLER',
            ],
            [
                'kode' => 'KP02A',
                'nama_tujuan' => 'SUB DIVISI SATUAN PENGAWASAN INTERN I',
            ],
            [
                'kode' => 'KP02B',
                'nama_tujuan' => 'SUB DIVISI SATUAN PENGAWASAN INTERN II',
            ],
            [
                'kode' => 'KP02C',
                'nama_tujuan' => 'SUB DIVISI MANAJEMEN AUDIT',
            ],
            [
                'kode' => 'KP02D',
                'nama_tujuan' => 'SUB DIVISI SATUAN PENGAWASAN INTERN III',
            ],
            [
                'kode' => 'KP03A',
                'nama_tujuan' => 'SUB DIVISI PERENCANAAN DAN PENGEMBANGAN BISNIS',
            ],
            [
                'kode' => 'KP03B',
                'nama_tujuan' => 'SUB DIVISI SISTEM MANAJEMEN',
            ],
            [
                'kode' => 'KP03C',
                'nama_tujuan' => 'SUB DIVISI PENGEMBANGAN DAN MANAJEMEN ASET',
            ],
            [
                'kode' => 'KP04A',
                'nama_tujuan' => 'SUB DIVISI PENELITIAN ON FARM',
            ],
            [
                'kode' => 'KP04B',
                'nama_tujuan' => 'SUB DIVISI PENELITIAN OFF FARM',
            ],
            [
                'kode' => 'KP06A',
                'nama_tujuan' => 'SUB DIVISI TEKNIK',
            ],
            [
                'kode' => 'KP06B',
                'nama_tujuan' => 'SUB DIVISI PENGOLAHAN',
            ],
            [
                'kode' => 'KP06C',
                'nama_tujuan' => 'SUB DIVISI K3 DAN LINGKUNGAN',
            ],
            [
                'kode' => 'KP06D',
                'nama_tujuan' => 'SUB DIVISI TEKNIK II',
            ],
            [
                'kode' => 'KP07A',
                'nama_tujuan' => 'SUB DIVISI SARANA PRODUKSI',
            ],
            [
                'kode' => 'KP07B',
                'nama_tujuan' => 'SUB DIVISI BUDIDAYA',
            ],
            [
                'kode' => 'KP07C',
                'nama_tujuan' => 'SUB DIVISI PEMBENIHAN DAN PENGEMBANGAN LAHAN',
            ],
            [
                'kode' => 'KP08A',
                'nama_tujuan' => 'SUB DIVISI QA BAHAN BAKU',
            ],
            [
                'kode' => 'KP08B',
                'nama_tujuan' => 'SUB DIVISI QA PRODUK GULA',
            ],
            [
                'kode' => 'KP09A',
                'nama_tujuan' => 'SUB DIVISI PERENCANAAN DAN KESEJAHTERAAN',
            ],
            [
                'kode' => 'KP09B',
                'nama_tujuan' => 'SUB DIVISI REMUNERASI DAN MANAJEMEN KINERJA',
            ],
            [
                'kode' => 'KP09C',
                'nama_tujuan' => 'SUB DIVISI PERSONALIA DAN HUBUNGAN INDUSTRI',
            ],
            [
                'kode' => 'KP09D',
                'nama_tujuan' => 'SUB DIVISI UMUM',
            ],
            [
                'kode' => 'KP10A',
                'nama_tujuan' => 'SUB DIVISI STRATEGI TI',
            ],
            [
                'kode' => 'KP10B',
                'nama_tujuan' => 'SUB DIVISI OPERASIONAL TI',
            ],
            [
                'kode' => 'KP10C',
                'nama_tujuan' => 'SUB DIVISI KEAMANAN SIBER DAN AKSELERASI DIGITAL',
            ],
            [
                'kode' => 'KP11A',
                'nama_tujuan' => 'SUB DIVISI HUKUM PERUSAHAAN',
            ],
            [
                'kode' => 'KP11B',
                'nama_tujuan' => 'SUB DIVISI HUBUNGAN KELEMBAGAAN',
            ],
            [
                'kode' => 'KP11C',
                'nama_tujuan' => 'SUB DIVISI LAYANAN HUKUM',
            ],
            [
                'kode' => 'KP11D',
                'nama_tujuan' => 'SUB DIVISI NON LITIGASI',
            ],
            [
                'kode' => 'KP12A',
                'nama_tujuan' => 'SUB DIVISI PEMASARAN',
            ],
            [
                'kode' => 'KP12B',
                'nama_tujuan' => 'SUB DIVISI PELANGGAN',
            ],
            [
                'kode' => 'KP13A',
                'nama_tujuan' => 'SUB DIVISI PRODUKSI DAN PENGEMBANGAN TEBU RAKYAT',
            ],
            [
                'kode' => 'KP13B',
                'nama_tujuan' => 'SUB DIVISI TEBANG MUAT ANGKUT',
            ],
            [
                'kode' => 'KP14A',
                'nama_tujuan' => 'SUB DIVISI AKUNTANSI',
            ],
            [
                'kode' => 'KP14B',
                'nama_tujuan' => 'SUB DIVISI PAJAK DAN ASURANSI',
            ],
            [
                'kode' => 'KP15A',
                'nama_tujuan' => 'SUB DIVISI PENGADAAN I',
            ],
            [
                'kode' => 'KP15B',
                'nama_tujuan' => 'SUB DIVISI PENGADAAN II',
            ],
            [
                'kode' => 'KP16A',
                'nama_tujuan' => 'SUB DIVISI PERENCANAAN ANGGARAN',
            ],
            [
                'kode' => 'KP16B',
                'nama_tujuan' => 'SUB DIVISI MANAJEMEN KEUANGAN',
            ],
            [
                'kode' => 'KP16C',
                'nama_tujuan' => 'SUB DIVISI HARGA PERKIRAAN SENDIRI',
            ],
            [
                'kode' => 'KP17A',
                'nama_tujuan' => 'SUB DIVISI MANAJEMEN RISIKO KORPORAT (ERM)',
            ],
            [
                'kode' => 'KP17B',
                'nama_tujuan' => 'SUB DIVISI MANAJEMEN RISIKO OPERASIONAL DAN BISNIS',
            ],
            [
                'kode' => 'KP17C',
                'nama_tujuan' => 'SUB DIVISI KEPATUHAN',
            ],
            [
                'kode' => 'KP18A',
                'nama_tujuan' => 'SUB DIVISI ORGANISASI DAN BUDAYA',
            ],
            [
                'kode' => 'KP18B',
                'nama_tujuan' => 'SUB DIVISI PENDIDIKAN DAN PELATIHAN',
            ],
            [
                'kode' => 'KP18C',
                'nama_tujuan' => 'SUB DIVISI KARIR DAN ASESMEN',
            ],
            [
                'kode' => 'RG00',
                'nama_tujuan' => 'SELURUH REGIONAL',
            ],
            [
                'kode' => 'RG01',
                'nama_tujuan' => 'SUMATERA I',
            ],
            [
                'kode' => 'RG02',
                'nama_tujuan' => 'SUMATERA II',
            ],
            [
                'kode' => 'RG03',
                'nama_tujuan' => 'JATENG',
            ],
            [
                'kode' => 'RG04',
                'nama_tujuan' => 'JATIM I',
            ],
            [
                'kode' => 'RG05',
                'nama_tujuan' => 'JATIM II',
            ],
            [
                'kode' => 'RG06',
                'nama_tujuan' => 'JATIM III',
            ],
            [
                'kode' => 'RG07',
                'nama_tujuan' => 'JATIM IV',
            ],
            [
                'kode' => 'RG08',
                'nama_tujuan' => 'SULAWESI',
            ],

            [
                'kode' => 'SG00',
                'nama_tujuan' => 'SELURUH PABRIK GULA',
            ],
            [
                'kode' => 'SG01',
                'nama_tujuan' => 'PG KWALA MADU',
            ],
            [
                'kode' => 'SG02',
                'nama_tujuan' => 'PG SEI SEMAYANG',
            ],
            [
                'kode' => 'SG03',
                'nama_tujuan' => 'PG BUNGA MAYANG',
            ],
            [
                'kode' => 'SG04',
                'nama_tujuan' => 'PG CINTA MANIS',
            ],
            [
                'kode' => 'SG05',
                'nama_tujuan' => 'PG PANGKA',
            ],
            [
                'kode' => 'SG06',
                'nama_tujuan' => 'PG RENDENG',
            ],
            [
                'kode' => 'SG07',
                'nama_tujuan' => 'PG MOJO',
            ],
            [
                'kode' => 'SG08',
                'nama_tujuan' => 'PG TASIKMADU',
            ],
            [
                'kode' => 'SG09',
                'nama_tujuan' => 'PG SRAGI',
            ],
            [
                'kode' => 'SG10',
                'nama_tujuan' => 'PG KREMBOONG',
            ],
            [
                'kode' => 'SG11',
                'nama_tujuan' => 'PG GEMPOLKREP',
            ],
            [
                'kode' => 'SG12',
                'nama_tujuan' => 'PG DJOMBANG BARU',
            ],
            [
                'kode' => 'SG13',
                'nama_tujuan' => 'PG TJOEKIR',
            ],
            [
                'kode' => 'SG14',
                'nama_tujuan' => 'PG LESTARI',
            ],
            [
                'kode' => 'SG15',
                'nama_tujuan' => 'PG MERITJAN',
            ],
            [
                'kode' => 'SG16',
                'nama_tujuan' => 'PG PESANTREN BARU',
            ],
            [
                'kode' => 'SG17',
                'nama_tujuan' => 'PG NGADIREDJO',
            ],
            [
                'kode' => 'SG18',
                'nama_tujuan' => 'PG MODJOPANGGOONG',
            ],
            [
                'kode' => 'SG19',
                'nama_tujuan' => 'PG SOEDHONO',
            ],
            [
                'kode' => 'SG20',
                'nama_tujuan' => 'PG POERWODADIE',
            ],
            [
                'kode' => 'SG21',
                'nama_tujuan' => 'PG REDJOSARIE',
            ],
            [
                'kode' => 'SG22',
                'nama_tujuan' => 'PG PAGOTTAN',
            ],
            [
                'kode' => 'SG23',
                'nama_tujuan' => 'PG KEDAWOENG',
            ],
            [
                'kode' => 'SG24',
                'nama_tujuan' => 'PG WONOLANGAN',
            ],
            [
                'kode' => 'SG25',
                'nama_tujuan' => 'PG GENDING',
            ],
            [
                'kode' => 'SG26',
                'nama_tujuan' => 'PG DJATIROTO',
            ],
            [
                'kode' => 'SG27',
                'nama_tujuan' => 'PG SEMBORO',
            ],
            [
                'kode' => 'SG28',
                'nama_tujuan' => 'PG OLEAN',
            ],
            [
                'kode' => 'SG29',
                'nama_tujuan' => 'PG WRINGIN ANOM',
            ],
            [
                'kode' => 'SG30',
                'nama_tujuan' => 'PG PANDJI',
            ],
            [
                'kode' => 'SG31',
                'nama_tujuan' => 'PG ASSEMBAGOES',
            ],
            [
                'kode' => 'SG32',
                'nama_tujuan' => 'PG PRADJEKAN',
            ],
            [
                'kode' => 'SG33',
                'nama_tujuan' => 'PG GLENMORE',
            ],
            [
                'kode' => 'SG34',
                'nama_tujuan' => 'PG BONE',
            ],
            [
                'kode' => 'SG35',
                'nama_tujuan' => 'PG CAMMING',
            ],
            [
                'kode' => 'SG36',
                'nama_tujuan' => 'PG TAKALAR',
            ],
            [
                'kode' => 'MK00',
                'nama_tujuan' => 'SELURUH SEVP MKSO',
            ],
            [
                'kode' => 'MK01',
                'nama_tujuan' => 'SEVP KOORDINATOR KSO',
            ],
            [
                'kode' => 'MK02',
                'nama_tujuan' => 'SEVP OPERASIONAL KSO',
            ],

            [
                'kode' => 'KB00',
                'nama_tujuan' => 'SELURUH KEPALA BAGIAN',
            ],
            [
                'kode' => 'KB01',
                'nama_tujuan' => 'KEPALA BAGIAN OPERASIONAL',
            ],
            [
                'kode' => 'KB02',
                'nama_tujuan' => 'KEPALA BAGIAN BUSINESS SUPPORT',
            ],

            [
                'kode' => 'RK00',
                'nama_tujuan' => 'SELURUH CLUSTER HEAD',
            ],
            [
                'kode' => 'RK01',
                'nama_tujuan' => 'CLUSTER HEAD SUMATERA I',
            ],
            [
                'kode' => 'RK02',
                'nama_tujuan' => 'CLUSTER HEAD SUMATERA II',
            ],
            [
                'kode' => 'RK03',
                'nama_tujuan' => 'CLUSTER HEAD SULAWESI',
            ],
            [
                'kode' => 'SK00',
                'nama_tujuan' => 'SELURUH KEBUN',
            ],
            [
                'kode' => 'SK01',
                'nama_tujuan' => 'KEBUN KWALA MADU',
            ],
            [
                'kode' => 'SK02',
                'nama_tujuan' => 'KEBUN SEI SEMAYANG',
            ],
            [
                'kode' => 'SK03',
                'nama_tujuan' => 'KEBUN BUNGA MAYANG',
            ],
            [
                'kode' => 'SK04',
                'nama_tujuan' => 'KEBUN CINTA MANIS 1',
            ],
            [
                'kode' => 'SK05',
                'nama_tujuan' => 'KEBUN CINTA MANIS 2',
            ],
            [
                'kode' => 'SK06',
                'nama_tujuan' => 'KEBUN JATENG',
            ],
            [
                'kode' => 'SK07',
                'nama_tujuan' => 'KEBUN AGROFORESTRY',
            ],
            [
                'kode' => 'SK08',
                'nama_tujuan' => 'KEBUN DHOHO',
            ],
            [
                'kode' => 'SK09',
                'nama_tujuan' => 'KEBUN LUMAJANG RAYA',
            ],
            [
                'kode' => 'SK10',
                'nama_tujuan' => 'KEBUN BANYUWANGI RAYA',
            ],
            [
                'kode' => 'SK11',
                'nama_tujuan' => 'KEBUN KALITELEPAK',
            ],
            [
                'kode' => 'SK12',
                'nama_tujuan' => 'KEBUN MUMBUL',
            ],
            [
                'kode' => 'SK13',
                'nama_tujuan' => 'KEBUN BONE',
            ],
            [
                'kode' => 'SK14',
                'nama_tujuan' => 'KEBUN CAMMING',
            ],
            [
                'kode' => 'SK15',
                'nama_tujuan' => 'KEBUN TAKALAR',
            ],

            [
                'kode' => 'HDNUS',
                'nama_tujuan' => 'PT Perkebunan Nusantara III (Persero)',
            ],
            [
                'kode' => 'PN01',
                'nama_tujuan' => 'PT Perkebunan Nusantara I',
            ],
            [
                'kode' => 'PN04',
                'nama_tujuan' => 'PT Perkebunan Nusantara IV',
            ],
            [
                'kode' => 'IGG',
                'nama_tujuan' => 'PT Industri Gula Glenmore',
            ],
            [
                'kode' => 'BCN',
                'nama_tujuan' => 'PT Buma Cima Nusantara',
            ],
            [
                'kode' => 'LPP',
                'nama_tujuan' => 'Lembaga Pendidikan Perkebunan',
            ],
            [
                'kode' => 'P3GI',
                'nama_tujuan' => 'Pusat Penelitian Perkebunan Gula Indonesia',
            ],
            [
                'kode' => 'KPBN',
                'nama_tujuan' => 'PT Kharisma Pemasaran Bersama Nusantara',
            ],
            [
                'kode' => 'DAPEB',
                'nama_tujuan' => 'Dana Pensiun Perkebunan',
            ],
            [
                'kode' => 'KBUMN',
                'nama_tujuan' => 'Kementerian Badan Usaha Milik Negara',
            ],
            [
                'kode' => 'DEKOM',
                'nama_tujuan' => 'Dewan Komisaris',
            ],
            [
                'kode' => 'ORKOM',
                'nama_tujuan' => 'Organ Dewan Komisaris',
            ],
            [
                'kode' => 'BANEG',
                'nama_tujuan' => 'Bank BUMN',
            ],
            [
                'kode' => 'BASWA',
                'nama_tujuan' => 'Bank Swasta',
            ],
            [
                'kode' => 'INMIL',
                'nama_tujuan' => 'Instansi Militer',
            ],
            [
                'kode' => 'INSIP',
                'nama_tujuan' => 'Instansi Sipil',
            ],
            [
                'kode' => 'RUPA',
                'nama_tujuan' => 'Perseorangan, umum, dan lain pihak sebagainya',
            ],
            [
                'kode' => 'PESWA',
                'nama_tujuan' => 'Perusahaan umum/swasta, dan badan hukum lainnya',
            ],
            [
                'kode' => 'PERSE',
                'nama_tujuan' => 'Perseorangan internal',
            ],

                ],
        ['kode'],
        ['nama_tujuan']
    );

    }
}

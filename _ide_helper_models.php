<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_detail
 * @property int $id_transaksi
 * @property int $id_pupuk
 * @property int $jml_beli
 * @property numeric $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pupuk $pupuk
 * @property-read \App\Models\Transaksi $transaksi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereIdDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereIdPupuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereIdTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereJmlBeli($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailTransaksi whereUpdatedAt($value)
 */
	class DetailTransaksi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pencairan
 * @property int $id_mitra
 * @property numeric $jml_transfer
 * @property string $tgl_transfer
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $mitra
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereIdMitra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereIdPencairan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereJmlTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereTglTransfer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pencairan whereUpdatedAt($value)
 */
	class Pencairan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pupuk
 * @property string $nama_pupuk
 * @property numeric $harga_subsidi
 * @property numeric $stok
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetailTransaksi> $rincianTransaksi
 * @property-read int|null $rincian_transaksi_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereHargaSubsidi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereIdPupuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereNamaPupuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereStok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pupuk whereUpdatedAt($value)
 */
	class Pupuk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_rekonsiliasi
 * @property int $id_transaksi
 * @property int $id_admin
 * @property string $tgl_verifikasi
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $admin
 * @property-read \App\Models\Transaksi $transaksi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereIdAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereIdRekonsiliasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereIdTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereTglVerifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rekonsiliasi whereUpdatedAt($value)
 */
	class Rekonsiliasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_transaksi
 * @property int $id_petani
 * @property int $id_mitra
 * @property string $tgl_transaksi
 * @property numeric $total_harga
 * @property string $status
 * @property string $status_pengambilan
 * @property string $qr_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $mitra
 * @property-read \App\Models\User $petani
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetailTransaksi> $rincian
 * @property-read int|null $rincian_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereIdMitra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereIdPetani($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereIdTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereStatusPengambilan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTglTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTotalHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereUpdatedAt($value)
 */
	class Transaksi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $nama_mitra
 * @property string|null $email
 * @property string $nik_nip
 * @property string $password
 * @property string $role
 * @property string $status_akun
 * @property string|null $alasan_penolakan
 * @property int|null $verified_by
 * @property string|null $alamat
 * @property string|null $no_rek
 * @property mixed $saldo_app
 * @property string|null $jenis_kelamin
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $validator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $verifiedUsers
 * @property-read int|null $verified_users_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlasanPenolakan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNamaMitra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNikNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoRek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSaldoApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatusAkun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerifiedBy($value)
 */
	class User extends \Eloquent {}
}


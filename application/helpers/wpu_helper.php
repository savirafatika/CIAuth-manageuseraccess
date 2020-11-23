<?php
// Fungsi ini melakukan 2 hal :
// 1. cek sudah login belum
// 2. jika sudah login cek role id nya admin / user
function is_logged_in()
{
   // helper tidak mengenali $this makanya buat instans ci pake fungsi get_instance untuk memanggil library ci
   $ci = get_instance();
   //jika blm login / tidak ada sessionnya dilempar ke login page:
   if (!$ci->session->userdata('email')) {
      redirect('auth');
   } else {
      //jika login/ada session cek role_id nya brp
      $role_id = $ci->session->userdata('role_id');
      //dan cek mau akses menu mana, menu=controller, segment=uri urutan ke 1
      $menu = $ci->uri->segment(1);

      //dapatkan menu_id nya untuk mengecek role_id mana boleh akses menu_id mana seperti tabel user_access_menu
      $queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
      $menu_id = $queryMenu['id'];

      //setelah mendapat role_id dan menu_id, cek ada tidak di tabel user_access-menu
      // select * user_access_menu where role_id = role id yang didefinisikan diatas dan menu_id = menu id didefinisikan diatas
      $userAccess = $ci->db->get_where('user_access_menu', [
         'role_id' => $role_id,
         'menu_id' => $menu_id
      ]);

      //jika data yang diambil dari $userAccess barisnya <1 yaitu 0 atau tidak ada maka lempar ke blocked page
      if ($userAccess->num_rows() < 1) {
         redirect('auth/blocked');
      }
   }
}

function check_access($role_id, $menu_id)
{
   $ci = get_instance();

   // mengecek role_id & menu_id di tbl user_access_menu jika kedua angka tidak ada atau salah satu tidak ada atau tidak cocok maka checkbox pada Access menu sidebar Role tidak akan checked
   $ci->db->where('role_id', $role_id);
   $ci->db->where('menu_id', $menu_id);
   $result = $ci->db->get('user_access_menu');

   // jika hasil pengecekan $result ada atau ditemukan baris >0 maka dafault checkbox Access akan checked
   if ($result->num_rows() > 0) {
      return "checked='checked'";
   }
}

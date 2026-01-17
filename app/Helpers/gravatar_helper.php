<?php

if (!function_exists('gravatar_url')) {
    /**
     * Gera a URL do Gravatar para um email
     *
     * @param string $email Email do usuário
     * @param int $size Tamanho da imagem (padrão: 80)
     * @param string $default Tipo de imagem padrão (identicon, monsterid, wavatar, retro, robohash, ou URL)
     * @return string URL do Gravatar
     */
    function gravatar_url(string $email, int $size = 80, string $default = 'identicon'): string
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
    }
}

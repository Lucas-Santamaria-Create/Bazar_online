<?php
/**
 * Clase que se encarga de obtener citas aleatorias desde la API Quotable
 * y traducir textos usando la API de LibreTranslate.
 */
class QuotableService
{
    /**
     * URL base para obtener citas aleatorias en inglés desde Quotable
     * @var string
     */
    private $quotableApiUrl = 'https://api.quotable.io/random';

    /**
     * URL base para traducir textos con LibreTranslate
     * @var string
     */
    private $translateApiUrl = 'https://libretranslate.de/translate';

    /**
     * Obtiene una cita aleatoria desde la API de Quotable.
     *
     * Realiza una petición HTTP GET a la API para obtener una cita
     * y el autor correspondiente.
     */
    public function getRandomQuote()
    {
        $author = '';
        $quote = '';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->quotableApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: PHP'
            ],
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            error_log("Error al obtener la cita de Quotable: $err");
            return ['quote' => 'No se pudo obtener la cita. Error: ' . htmlspecialchars($err), 'author' => $author];
        }

        $data = json_decode($response, true);
        if (!isset($data['content']) || !isset($data['author'])) {
            error_log("Respuesta inválida de Quotable: " . $response);
            return ['quote' => 'No se recibió una cita válida de la API.', 'author' => ''];
        }

        $quote = $data['content'];
        $author = $data['author'];

        return ['quote' => $quote, 'author' => $author];
    }

    /**
     * Traduce un texto de un idioma fuente a un idioma destino usando la API LibreTranslate.
     */
    public function translateText($text, $source = 'en', $target = 'es')
    {
        $translatedText = '';

        $translate = json_encode([
            'q' => $text,
            'source' => $source,
            'target' => $target,
            'format' => 'text'
        ]);

        $opts = [
            'http' => [
                'method' => 'POST',
                'header'  => "Content-Type: application/json",
                'content' => $translate
            ]
        ];

        $context = stream_context_create($opts);
        $response = @file_get_contents($this->translateApiUrl, false, $context);

        if ($response !== false) {
            $decoded = json_decode($response, true);
            if ($decoded !== null && isset($decoded['translatedText'])) {
                $translatedText = $decoded['translatedText'];
            } else {
                error_log("Estructura inesperada de la respuesta de LibreTranslate: " . print_r($decoded, true));
                $translatedText = 'No se pudo traducir la frase.';
            }
        } else {
            error_log("Error al traducir con LibreTranslate.");
            $translatedText = 'Error al traducir.';
        }

        return $translatedText;
    }

    /**
     * Obtiene una cita aleatoria y la traduce al español.
     *
     * Llama a getRandomQuote() para obtener la cita original y luego
     * la traduce usando translateText(). En caso de error, retorna la cita original.
     * 
     */
    public function getRandomQuoteInSpanish()
    {
        $quoteData = $this->getRandomQuote();
        $quote = $quoteData['quote'] ?? '';
        $author = $quoteData['author'] ?? '';

        // Aquí se tendria que llamar a translateText para traducir
        // $translatedQuote = $this->translateText($quote, 'en', 'es');
        // if ($translatedQuote) {
        //     $quote = $translatedQuote;
        // }

        return ['quote' => $quote, 'author' => $author];
    }
}

@php
    $path = base_path("node_modules/lucide-static/icons/{$name}.svg");

    if (! file_exists($path)) {
        // Fallback: empty placeholder with correct size, so layout doesn't jump
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>';
    } else {
        $svg = file_get_contents($path);
    }

    // Lucide SVGs are 24x24 viewBox. We let the SVG scale naturally by
    // setting width/height to the requested size in pixels, keeping the viewBox.
    $svg = preg_replace('/\swidth="[^"]*"/', '', $svg, 1);
    $svg = preg_replace('/\sheight="[^"]*"/', '', $svg, 1);

    // Inject our size + class into the root <svg> element
    $sizeAttr  = 'width="'.$size.'" height="'.$size.'"';
    $classAttr = $class ? 'class="'.$class.'"' : '';

    // Add attributes right after the first "<svg"
    $svg = preg_replace('/<svg\b/', '<svg '.$sizeAttr.' '.$classAttr, $svg, 1);
@endphp

{!! $svg !!}
@props([
    'name',
    'alias' => null,
    'class' => '',
    'size' => 'w-5 h-5',
])

@php
    // If alias is provided, look up the icon name from config
    if ($alias) {
        // Parse alias like 'actions.edit' or 'navigation.dashboard'
        $parts = explode('.', $alias);
        $category = $parts[0] ?? null;
        $key = $parts[1] ?? null;
        
        if ($category && $key && isset(config("icons.{$category}.{$key}"))) {
            $name = config("icons.{$category}.{$key}");
        }
    }
    
    $svgPath = base_path("node_modules/lineicons/assets/svgs/regular/{$name}.svg");
    
    if (!file_exists($svgPath)) {
        $svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" fill="currentColor" opacity="0.1"/></svg>';
    } else {
        $svg = file_get_contents($svgPath);
        
        // Remove existing fill attributes from the SVG content
        $svg = preg_replace('/fill="[^"]*"/', '', $svg);
        
        // Add dark/light mode fill using currentColor
        $svg = str_replace('<path ', '<path fill="currentColor" ', $svg);
        $svg = str_replace('<circle ', '<circle fill="currentColor" ', $svg);
        $svg = str_replace('<rect ', '<rect fill="currentColor" ', $svg);
        $svg = str_replace('<polygon ', '<polygon fill="currentColor" ', $svg);
        
        // Remove width and height attributes to allow CSS sizing
        $svg = preg_replace('/width="[^"]*"/', '', $svg);
        $svg = preg_replace('/height="[^"]*"/', '', $svg);
        
        // Add class attribute to SVG tag
        $classes = trim("{$size} {$class}");
        $svg = str_replace('<svg ', "<svg class=\"{$classes}\" ", $svg);
    }
@endphp

{!! $svg !!}

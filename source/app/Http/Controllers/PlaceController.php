<?php

namespace App\Http\Controllers;

use App\Enums\OrderBy;
use App\Enums\TagOperator;
use App\Models\Place;
use App\Services\PlaceSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * If you have a Model class that describes a place (such as Restaurant.php or Karaoke.php),
 * you want its controller to extend this class.
 * Subclasses provide the model name (the view names derive from it) and the search service of their kind.
 * The controller only reads the request and renders views; the search itself is in App\Services\PlaceSearch.
 */
abstract class PlaceController extends Controller
{
    // Force the extending class to define this method
    abstract protected function getModelName(): string;

    /**
     * The search service of the kind, e.g. new RestaurantSearch
     *
     * @return PlaceSearch<covariant Model&Place>
     */
    abstract protected function search(): PlaceSearch;

    /**
     * Returns the Model class to use to read the data, e.g. App\Models\Restaurant.
     *
     * @return class-string<Model>
     */
    public function getModelClass(): string
    {
        /** @var class-string<Model> $class */
        $class = 'App\\Models\\'.$this->getModelName();

        return $class;
    }

    /**
     * Marks a view name built from the model name as a "view-string" for PHPStan (which cannot check that
     * a computed name is an existing view; the feature tests do).
     *
     * @return view-string
     */
    private function viewName(string $name): string
    {
        // @phpstan-ignore return.type
        return $name;
    }

    /**
     * Returns the view name for the place's details.
     *
     * @param  bool  $single  false (default) for the view that fits into the one page design,
     *                        true for the SINGLE details view (standalone page that can be shared)
     * @return view-string
     */
    public function getModelDetailsView(bool $single = false): string
    {
        // e.g. 'restaurants.details' for the Restaurant model
        $viewName = strtolower($this->getModelName()).'s.details';
        if ($single) {
            // e.g. 'restaurants.details-single'
            $viewName .= '-single';
        }

        return $this->viewName($viewName);
    }

    /**
     * Returns the view name for the rows of the results list, e.g. 'restaurants.results-data'.
     *
     * @return view-string
     */
    public function getModelResultsDataView(): string
    {
        return $this->viewName(strtolower($this->getModelName()).'s.results-data');
    }

    /**
     * Get the list to display (HTML fragment loaded by AJAX).
     * Parameters: op (AND/OR/ANDNOT/ORNOT), tags (comma separated), position (lat,lon), orderBy (distance/ratings/cost).
     */
    public function results(Request $request): View
    {
        // -----------------------------------
        // Reads the request parameters (from the query string)

        // Operator for the tags (AND unless the URL says OR, ANDNOT or ORNOT)
        $op = TagOperator::fromRequest($request->query('op'));

        // Tags as a comma separated list, made into an array
        $tags = $this->formatTags($request->query('tags'));

        // Current position from the client's GPS
        $position = $this->positionParam($request);

        // The sorting order for the results list (distance unless the URL says ratings or cost)
        $orderBy = OrderBy::fromRequest($request->query('orderBy'));

        // -----------------------------------
        // The search service reads from the cache or the DB, then fills the distances and sorts by them if required
        $search = $this->search();
        $places = $search->find($tags, $op, $orderBy);
        $places = $search->withDistances($places, $position, $orderBy);

        // -----------------------------------
        // Shortens the points to display in the list
        foreach ($places as $place) {
            $place->setAttribute('points', $this->ellipsis($place->getAttribute('points'), 50));
        }

        // Returns the view with the resulting places
        return view($this->getModelResultsDataView(), compact('places'));
    }

    /**
     * The "position" query parameter as a string (null when missing or not a scalar, e.g. position[]=x).
     */
    protected function positionParam(Request $request): ?string
    {
        $position = $request->query('position');

        return is_scalar($position) ? (string) $position : null;
    }

    /**
     * Make the tags into an array, if it is not already the case.
     * "ramen, noodles" becomes ['ramen', 'noodles']; empty input becomes null.
     *
     * @return array<int, string>|null
     */
    public function formatTags(mixed $tags): ?array
    {
        if (empty($tags)) {
            return null;
        }
        // Already a list (array or iterator): just make sure the keys are 0, 1, 2...
        if (is_iterable($tags)) {
            return array_values(is_array($tags) ? $tags : iterator_to_array($tags));
        }

        // Comma separated string: remove the spaces after the commas, then split
        return explode(',', str_replace(', ', ',', (string) $tags));
    }

    /**
     * Shorten a string to the given width and end it with "…" (the HTML entity counts in the width).
     */
    public function ellipsis(?string $string, int $length): string
    {
        return mb_strimwidth($string ?? '', 0, $length, '&hellip;');
    }
}

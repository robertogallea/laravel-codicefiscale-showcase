# Codice Fiscale Studio

A Laravel showcase application demonstrating every feature of `robertogallea/laravel-codicefiscale` v3. The package's own README is the canonical glossary for codice fiscale domain concepts (`CodiceFiscale`, `BirthPlace`, `Omocodia`, `Matcher`, and so on) and is not duplicated here — the terms below exist only to disambiguate names that collide between the package and this showcase's own code.

## Language

**Person (value object)**:
The package's immutable `Robertogallea\CodiceFiscale\Data\Person` — the input to `Generator` and the comparison target for `Matcher`. Never persisted, never has an id.
_Avoid_: "Person" alone when the Eloquent model is also in scope — always disambiguate.

**Person (record)**:
This showcase's own `App\Models\Person` Eloquent model, backing the People directory demo. Stores a `codice_fiscale` column cast to a domain `CodiceFiscale` via `CodiceFiscaleCast`.
_Avoid_: "Person" alone, "PersonModel"

**Real repository**:
The package's default `BirthPlaceRepository` binding (`CompositeBirthPlaceRepository`), backed by real ANPR/MAECI data fetched via `codice-fiscale:update-places`. This is what the hero Calculate/Decode tool always uses.
_Avoid_: "default repository" alone — ambiguous with Laravel's own notion of a default container binding.

**Demo repository**:
A fictional, hand-authored `BirthPlaceRepository` implementation (Atlantis, Narnia, Wakanda, ...) that exists solely to prove the repository contract is swappable, on the standalone Repository-swap Explore page. Never used by the hero tool.
_Avoid_: "custom repository" alone — always name which one when both are in play.

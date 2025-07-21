# IdeaBoard Lite

**Plugin Name:** IdeaBoard Lite
**Description:** Két-boardos ötletbeküldő rendszer AJAX-os, e-mail és név alapú szavazással.
**Author:** Business Bloom Dev
**Version:** 4.2

## Felhasználói bemutató

Az IdeaBoard Lite egy WordPress plug-in, amely két különálló "boardra" (ügyfél és dolgozó) bontva teszi lehetővé ötletek beküldését és szavazását. A plug-in a `ideaboard-lite.php` fájlban található.

### Fő funkciók

1. **Egyedi tartalomtípus és taxonómia**
   - A plug-in létrehoz egy `idea` nevű post type-ot és hozzá `board` taxonómiát, amelyben a két boardot kezeli (például "ugyfel" és "dolgozo").

2. **Beküldő űrlapok**
   - Két shortcode áll rendelkezésre az ötletek beküldéséhez:
     - `[idea_form_client]` ügyfél beküldéshez
     - `[idea_form_employee]` dolgozói beküldéshez; ez ellenőrzi, hogy a megadott e-mail a beállított céges domainnel (például `@ceg.hu`) végződjön.
     - A beküldő neve és e-mail címe mellett opcionálisan megadható egy **telefonszám** is, amelyet az adminisztrátorok a beérkezett értesítő e-mailben látnak.
     - A beküldés előtt a felhasználónak el kell fogadnia az adatkezelési tájékoztatót egy kötelező GDPR jelölőnégyzettel. Mindkét űrlap tartalmaz honeypot mezőt a spamszűréshez.

3. **Szavazás kezelése**
    - A `[idea_list]` shortcode listázza a választott board ötleteit, megjeleníti a szavazatszámot, és egy AJAX-al működő szavazó űrlapot ad. A "Megadom az adataimat" checkbox bepipálásával a szavazó önkéntesen megadhatja nevét és e-mail címét (dolgozói board esetén csak a megadott céges domainnel) , amelyet a rendszer eltárol és az adminfelületen a "Szavazók" metaboxban megjelenít.

4. **Adminisztrációs felület és értesítések**
   - A backenden megjelenik egy külön menüpont "IdeaBoard" néven a szavazások és ötletek kezeléséhez, illetve egy beállítási oldal, ahol megadhatók az értesítési e-mail címek.
   - Új ötlet beküldésekor a `notifications.php` modul e-mailt küld a megadott címekre a beküldött adatokkal.

5. **Stílus és JavaScript**
   - A frontendet a `ideaboard-style.css` és a `vote.js` fájlok formázzák és kezelik. A szkriptek a plug-in betöltésekor automatikusan enqueued-re kerülnek.

### Használat

1. **Telepítés**
   - Másolja a repository tartalmát a WordPress `wp-content/plugins` könyvtárába.
   - Aktiválja az "IdeaBoard Lite" plug-int az adminisztrációs felületen.

2. **Beállítások**
   - A "Beállítások" almenüben adja meg, milyen e-mail cím(ek)re érkezzen értesítés új ötlet beküldésekor.

3. **Ötletbeküldés**
   - A megfelelő oldalon illessze be a `[idea_form_client]` vagy `[idea_form_employee]` shortcode-ot. A dolgozói űrlap csak a beállított céges e-mail végződést engedi.
   - A beküldő neve és e-mail címe mellett megadható telefonszám, amely az értesítő e-mailben is szerepel.

4. **Ötletek listázása és szavazás**
   - A `[idea_list board="ugyfel"]` vagy `[idea_list board="dolgozo"]` shortcode megjeleníti az adott boardhoz tartozó ötleteket. A látogatók opcionálisan megadhatják nevüket és e-mail címüket egy külön checkbox kipipálása után. E-mail megadásakor a dolgozói boardon csak a beállított céges végződéssel lehet szavazni. A szavazatok számát a rendszer automatikusan frissíti.

5. **Admin funkciók**
   - Az admin felületen az ötletek listájában látható a beküldők neve, e-mailje és az aktuális szavazatszám. Az egyes ötletek szerkesztőoldalán "Szavazók" metabox jelenik meg, amely felsorolja, kik adtak le érvényes szavazatot.

Ezzel a plug-innel egyszerűen hozható létre egy, a felhasználók ötleteit gyűjtő és azokra szavazást biztosító rendszer WordPressen belül. A honeypot mezők és az e-mail ellenőrzések alapvető spamszűrést is nyújtanak, míg a beállítható értesítések az adminisztrátoroknak segítik az új ötletek nyomon követését.
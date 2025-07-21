# IdeaBoard Lite

Két-boardos ötletbeküldő rendszer WordPresshez – AJAX-os szavazással, e-mail ellenőrzéssel és admin felügyelettel. Ideális cégeknek, akik szeretnék begyűjteni az ügyfelek vagy munkatársak ötleteit és visszajelzéseit.

**Szerző:** [Tolvaj Sándor / Business Bloom Consulting](https://businessbloom.consulting) 
**Licenc:** [GPLv2 vagy újabb](https://www.gnu.org/licenses/gpl-2.0.html)  
**Verzió:** 1.0  
**Tesztelve:** WordPress 6.8.2

---

## Mire jó?

Az IdeaBoard Lite lehetővé teszi, hogy két külön felületen (ügyfél és dolgozói) fogadj be ötleteket és engedélyezz szavazást. A rendszer mindkét oldalra külön űrlapot biztosít, ellenőrzi az e-mail címeket, és segít nyomon követni a beküldött ötleteket és a rájuk leadott szavazatokat.

---

## Fő funkciók

- Egyedi `idea` tartalomtípus és `board` taxonómia (`ugyfel`, `dolgozo`)
- Két külön beküldő űrlap:
  - `[idea_form_client]` – ügyfeleknek
  - `[idea_form_employee]` – dolgozóknak (csak céges e-mailt enged)
- GDPR checkbox, honeypot spamszűrés, opcionális telefonszám mező
- AJAX-alapú szavazás: `[idea_list board="ugyfel|dolgozo"]`
- Opcionális név és e-mail megadás szavazásnál
- Admin oldalon szavazók metabox és ötletkezelés
- E-mail értesítések új beküldésekről
- Automatikus CSS/JS betöltés (`ideaboard-style.css`, `vote.js`)

---

## Használat

1. Másold a plugint a `wp-content/plugins/` könyvtárba.
2. Aktiváld a WordPress admin felületen.
3. A „Beállítások” menüpontban add meg, hová küldje az értesítéseket.
4. Oldalakon használd a shortcode-okat:
   - `[idea_form_client]` vagy `[idea_form_employee]`
   - `[idea_list board="ugyfel"]` vagy `[idea_list board="dolgozo"]`

---

## Kinek ajánlott?

- Cégeknek, akik visszajelzést várnak ügyfelektől
- Szervezeteknek, akik belső ötletelést szeretnének
- Bármilyen WordPress-alapú oldalon, ahol fontos a közösségi részvétel

---

## English

IdeaBoard Lite is a WordPress plugin that lets you collect and vote on ideas in two separate boards: one for **clients**, one for **employees**. Ideal for organizations that want structured feedback and simple voting.

---

### Features

- Custom post type: `idea`, and taxonomy: `board` (`client`, `employee`)
- Two submission forms:
  - `[idea_form_client]` – for client submissions
  - `[idea_form_employee]` – for employee submissions (with email domain validation)
- Required GDPR checkbox, honeypot spam protection, optional phone number field
- AJAX-powered voting list with `[idea_list board="client|employee"]`
- Optional name/email for voters (email domain check on employee board)
- Admin panel with idea list and voter details
- Email notifications for new ideas
- Auto-enqueued styles and scripts (`ideaboard-style.css`, `vote.js`)

---

### Usage

1. Upload plugin to `wp-content/plugins/`.
2. Activate it in the admin area.
3. Set up notification emails under “Settings”.
4. Add the shortcodes to your pages:
   - `[idea_form_client]`, `[idea_form_employee]`
   - `[idea_list board="client"]`, `[idea_list board="employee"]`

---

### License

This plugin is licensed under the [GNU GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

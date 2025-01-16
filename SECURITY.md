# Hubzilla Security Policy

The [Hubzilla] Project takes security, privacy and user control over personal data seriously. We ask that any security issues be disclosed to us in a responsible manner to allow us time to remediate the issues, and site administrators time to upgrade before information about the issue is made public.

This document outlines security procedures and policies for the Hubzilla project. It covers the following components:

* The Hubzilla core repository: https://framagit.org/hubzilla/core
* The official addon repository: https://framagit.org/hubzilla/addons
* The official themes repository: https://framagit.org/hubzilla/themes
* The official widgets repository: https://framagit.org/hubzilla/widgets

## Coordinated Disclosure Guidelines

We are committed to working with security researchers to verify, reproduce, and respond to legitimate reported vulnerabilities. You can help us by following these simple guidelines:

* Submit suspected vulnerabilities by email to `security@hubzilla.org`, or as a confidential issue in the relevant repository listed above.
* Provide clear instructions on how to reproduce the issue, and if possible, a minimal Proof of Concept (PoC) exploit.
* We will acknowledge your submission as soon as we can, and will keep you updated as it is being processed. We may ask for more information, or clarifications about the issue or the steps to reproduce it during this time.
* We will assign a CVE to the issue once it is confirmed.
* We will do our best to fix the issue as soon as we can after it has been confirmed. We request that information about the vulnerability or details about how to exploit it is not disclosed to other parties until after the fix is released and some time has passed, to allow site administrators to upgrade. We will normally make the CVE public one month after a fix has been released. (This grace period can differ based on severity, and can be negotiated.)
* Please perform all tests against a local instance of the software, and refrain from running any Denial of Service or automated testing tools against public hubs or the project managers (and their partners') infrastructure.
* If the issue belongs to a third party module that we depend on, we may help with reporting it upstream if the submitter wants us to.

## Comments on this Policy

We welcome comments and suggestions for improving this policy. You can reach us at:

* Our ticketing system: https://framagit.org/hubzilla/core/-/issues
* By sending us an email at `security@hubzilla.org`.

[Hubzilla]: https://hubzilla.org

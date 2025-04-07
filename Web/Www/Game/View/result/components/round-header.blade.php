<lit-round-result-header
  :countryCca2="selectedRound.round?.country_cca2"
  :countryName="selectedRound.round?.country_name"
  :countrySubdivisionName="selectedRound.round?.country_subdivision_name"
  :userGuess="selectedRound.userGuess ? JSON.stringify({
    countryCca2: selectedRound.userGuess?.country_cca2,
    countryName: selectedRound.userGuess?.country_name,
    countryMatch: selectedRound.userGuess?.country_match,
    countrySubdivisionMatch: selectedRound.userGuess?.country_subdivision_match,
    detailedPoints: selectedRound.userGuess?.detailed_points,
    distanceMeters: selectedRound.userGuess?.distance_meters,
    flagFilePath: selectedRound.userGuess?.flag_file_path,
    roundedPoints: selectedRound.userGuess?.rounded_points,
    rank: selectedRound.userGuess?.rank
  }) : null"
  class="w-full">
</lit-round-result-header>
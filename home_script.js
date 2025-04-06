document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".hero-buttons .btn");
    
    buttons.forEach(button => {
        button.addEventListener("mouseover", function() {
            this.style.backgroundColor = "red";
        });
        
        button.addEventListener("mouseout", function() {
            this.style.backgroundColor = "green";
        });
    });

      function searchJobs() {
        const jobContainer = document.getElementById("job-container");
        jobContainer.innerHTML = "";
        const jobs = [
            { title: "Software Developer", company: "Tech Corp", location: "New York" },
            { title: "Data Analyst", company: "Data Inc.", location: "San Francisco" },
            { title: "UI/UX Designer", company: "Creative Minds", location: "Los Angeles" }
        ];

        jobs.forEach(job => {
            const jobCard = document.createElement("div");
            jobCard.classList.add("job-card");
            jobCard.innerHTML = `<h3>${job.title}</h3><p>${job.company}</p><p>${job.location}</p>`;
            jobContainer.appendChild(jobCard);
        });
    }

    document.querySelector(".search-bar button").addEventListener("click", searchJobs);
});

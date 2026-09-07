/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

/**
 * TEMPORARY fixture standing in for the not-yet-built portal Projects API
 * (GET /api/portal/projects and GET /api/portal/projects/{project}).
 *
 * Shaped to match the contract proposed to the backend team. Once those
 * endpoints ship, delete this file and point `useProjectsData/useProjectData`
 * in `Pages/loaders.js` at `apiGet('/projects')` / `apiGet('/projects/{id}')` instead.
 */

const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const mockProjects = [
    {
        id: '0d6f7e2a-1a2b-4c3d-9e4f-000000000001',
        name: 'Office Renovation Projects',
        description: 'Renovation of the downtown office building.',
        start_date: '2026-07-01',
        target_completion_date: '2026-09-30',
        progress_percentage: 42,
    },
    {
        id: '0d6f7e2a-1a2b-4c3d-9e4f-000000000002',
        name: 'Campus Network Upgrade',
        description: 'Upgrading network infrastructure across campus buildings.',
        start_date: '2026-06-15',
        target_completion_date: '2026-12-01',
        progress_percentage: 10,
    },
];

const mockProjectDetails = {
    '0d6f7e2a-1a2b-4c3d-9e4f-000000000001': {
        id: '0d6f7e2a-1a2b-4c3d-9e4f-000000000001',
        name: 'Office Renovation Projects',
        pipelines: [
            {
                id: 'a1a1a1a1-0000-4000-8000-000000000001',
                name: 'Renovation Pipeline',
                groups: [
                    {
                        milestone_id: 'm1',
                        milestone_title: 'Lockable Interior Areas',
                        progress_percentage: 0,
                        entries: [
                            {
                                id: 'e1',
                                name: 'Lockable Interior Areas',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-28',
                            },
                        ],
                    },
                    {
                        milestone_id: 'm2',
                        milestone_title: 'Perforated Vinyl Applied to Windows',
                        progress_percentage: 0,
                        entries: [
                            {
                                id: 'e2',
                                name: 'Perforated Vinyl Installation',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-14',
                            },
                        ],
                    },
                    {
                        milestone_id: 'm3',
                        milestone_title: 'Security Cameras Installed',
                        progress_percentage: 0,
                        entries: [],
                    },
                    {
                        milestone_id: 'm4',
                        milestone_title: 'Security systems set up',
                        progress_percentage: 0,
                        entries: [
                            {
                                id: 'e3',
                                name: 'Security Camera Installation',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-21',
                            },
                            {
                                id: 'e4',
                                name: 'Access Code Door Lock Installation',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-21',
                            },
                        ],
                    },
                    {
                        milestone_id: null,
                        milestone_title: 'No Associated Milestone',
                        progress_percentage: null,
                        entries: [
                            {
                                id: 'e5',
                                name: 'Interior Decorating',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-28',
                            },
                            {
                                id: 'e6',
                                name: 'Coffee Maker Purchase',
                                stage: 'Planning',
                                start_date: null,
                                due: '2026-08-28',
                            },
                        ],
                    },
                ],
            },
        ],
    },
    '0d6f7e2a-1a2b-4c3d-9e4f-000000000002': {
        id: '0d6f7e2a-1a2b-4c3d-9e4f-000000000002',
        name: 'Campus Network Upgrade',
        pipelines: [
            {
                id: 'b2b2b2b2-0000-4000-8000-000000000001',
                name: 'Phase 1 - Buildings A/B',
                groups: [
                    {
                        milestone_id: 'm5',
                        milestone_title: 'Core Switch Replacement',
                        progress_percentage: 50,
                        entries: [
                            {
                                id: 'e7',
                                name: 'Install Core Switches',
                                stage: 'In Progress',
                                start_date: '2026-06-20',
                                due: '2026-07-10',
                            },
                        ],
                    },
                ],
            },
            {
                id: 'b2b2b2b2-0000-4000-8000-000000000002',
                name: 'Phase 2 - Buildings C/D',
                groups: [
                    {
                        milestone_id: null,
                        milestone_title: 'No Associated Milestone',
                        progress_percentage: null,
                        entries: [],
                    },
                ],
            },
        ],
    },
};

export async function fetchMockProjects() {
    await delay(150);

    return { data: mockProjects };
}

export async function fetchMockProject(projectId) {
    await delay(150);

    const project = mockProjectDetails[projectId];

    if (!project) {
        const error = new Error('Project not found');
        error.response = { status: 404 };
        throw error;
    }

    return { data: project };
}
